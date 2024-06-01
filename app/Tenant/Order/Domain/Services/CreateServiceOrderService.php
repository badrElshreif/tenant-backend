<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Helpers\MyFatoorah;
use App\Infrastructure\Helpers\Payment\PaymentService;
use App\Tenant\Order\Domain\Models\Order;
use App\Tenant\Order\Domain\Models\Status;
use App\Tenant\Order\Domain\Models\OrderService;
use App\PromoCode\Domain\Models\PromoCode;
use App\Tenant\Order\Domain\Models\PaymentMethod;
use App\Store\Domain\Models\Store;
use App\Product\Domain\Models\ProductView;
use App\Warranty\Domain\Models\Warranty;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\User\Domain\Models\User;
use DB;
use App\Notification\Domain\Notifications\OrderNotification;
use App\Tenant\Order\Domain\Models\BankTransfer;
use Illuminate\Support\Arr;
use App\Admin\Domain\Models\Admin;
use App\Tenant\Order\Domain\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Tenant\Order\Domain\Resources\OrderResource;
use MPDF;
use Illuminate\Support\Str;

class CreateServiceOrderService extends Service
{

    public $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService=$paymentService;
    }
    public function handle($data = [])
    {
        try {
            // Begin Transaction
            DB::beginTransaction();
            if(isset($data['user_id']) && !auth('api')->check())
                $user = User::findOrFail($data['user_id']);
            else
                $user = auth()->user();

            $data['user_id'] = $user->id;
            $data['status_id'] = status('order', 'new');
            // $store = Store::findOrFail($data['store_id']);
            // if($store->type != 'centers')
            //     return new GenericPayload(__('error.orders.centerIsRequired'), 422);
            $service = ProductView::findOrFail($data['service_id']);
            if($service->category->type != 'centers')
                return new GenericPayload(__('error.orders.serviceIsRequired'), 422);
            $store = $service->store;
            $data['store_id'] = optional($service->store)->id;
            $data['type'] = 'centers';
            $data['subtotal'] = $service->preview_fees;
            $data['order_added_tax'] = $data['subtotal'] * setting('order_added_tax') / 100;
            $data['total'] = $data['subtotal'] + $data['order_added_tax'];
            $data['promo_code_discount'] = 0.00;
            if(isset($data['promo_code'])){
                $promoCodeData = $this->validateCoupon($data['promo_code'], $data['total'], $store->id, $user->id);
                if($promoCodeData['msg'] != null)
                    return new GenericPayload($promoCodeData['msg'], 422);
                $coupon = $promoCodeData['promo_code'];
                $data['promo_code_id'] = $promoCodeData['promo_code']->id;
                $data['promo_code_discount'] = $promoCodeData['coupon_discount'];
            }
            $data['total'] -= $data['promo_code_discount'];
            // $order_min_cost = setting('order_min_cost') ? : 1000;
            // if($order_min_cost > $data['total'])
            //     return new GenericPayload(__('error.orders.orderMinCost'). $order_min_cost , 422);
            if(isset($data['use_wallet']) && $data['use_wallet'] == 1){
                $data['remain'] = $data['total'];
                $wallet = optional($user->transactions()->orderBy('id', 'desc')->first())->wallet_total ? : 0.00;

                if($wallet && $wallet > 0){
                    if($wallet > $data['total']){
                        $data['wallet_payout'] = $data['total'];
                        $data['remain'] = 0.00;
                        $data['wallet_total'] = $wallet - $data['total'];
                    }else{
                        $data['wallet_payout'] = $wallet;
                        $data['remain'] = $data['total'] - $wallet;
                        $data['wallet_total'] = 0.00;
                    }
                }

                if($data['remain'] > 0 && !isset($data['payment_method_id']))
                    return new GenericPayload(__('error.orders.requiredPaymentMethod'), 422);
                if(isset($data['payment_method_id'])){
                    $payment_method = paymentMethod::findOrFail($data['payment_method_id']);
                    if($payment_method->type == 'wallet' && $data['remain'] > 0)
                        return new GenericPayload(__('error.orders.walletNotEnough'), 422);
                }

                if($data['remain'] == 0.00){
                    $payment_method = paymentMethod::where('type', 'wallet')->firstOrFail();
                    $data['payment_method_id'] = $payment_method->id;
                }
            } else {
                if(!isset($data['payment_method_id']) && $data['remain'] > 0)
                    return new GenericPayload(__('error.orders.requiredPaymentMethod'), 422);

                $payment_method = paymentMethod::findOrFail($data['payment_method_id']);
            }

            //$application_dues_percentage = setting('application_dues');
            $data['application_dues_percentage'] = $store->application_dues > 0 ? $store->application_dues : setting('application_dues');
            if($data['application_dues_percentage'])
                $data['application_dues'] = $data['total'] * $data['application_dues_percentage'] / 100;

            if($payment_method->type=='online'){
                $data['is_paid']=0;
            }
            // create unique qr code
            $data['qr_code'] = qrCode();
            $data['invoice_file'] = time().'_'. Str::random(4).'.pdf';

            $order = Order::create($data);
            $order_items = $this->handleOrderItems($order, $service, Arr::only($data, ['subcategory_id', 'problem_description']));
            if($order_items != null)
                return new GenericPayload($order_items, 422);

            if(isset($data['use_wallet']) && $data['use_wallet'] == 1 && $wallet > 0){
                $user->transactions()->create([
                    'type' => 'pay_out',
                    'amount' => $data['wallet_payout'],
                    'wallet_total' => $data['wallet_total'],
                    'transactable_id' => $order->id,
                    'transactable_type' => 'App\Tenant\Order\Domain\Models\Order',
                    //'reason' => 'order',
                    'ar'  => ['reasons' => "تم استخدام مبلغ  {$order->wallet_payout} من الحفظة لاستخدامها فى الطلب  رقم {$order->id}"],
                    'en'  => ['reasons' => "{$order->wallet_payout} has been used from your wallet for order no. {$order->id}"],

                ]);
            }
            if($payment_method->type == 'bank_transfer'){
                if( !isset($data['depositor_name']) || !isset($data['deposit_amount']) || !isset($data['deposit_receipt']) || !isset($data['bank_account_id'])) {
                    DB::rollback();
                    return new GenericPayload(__('error.orders.requiredBankTransferData'), 422);
                }

                $remain = $data['total'];
                if(isset($data['remain']) && $data['remain'] > 0)
                    $remain = $data['remain'];

                if($data['deposit_amount'] < $remain){
                    DB::rollback();
                    //dd($remain);
                    return new GenericPayload(__('error.orders.lowTransferedMoney'), 422);
                }

                // if($data['deposit_amount'] < $order->remain){
                //     DB::rollback();
                //     return new GenericPayload(__('error.orders.lowTransferedMoney'), 422);
                // }
                $transfer = $order->bankTransfer()->create(Arr::only($data, ['depositor_name', 'deposit_amount', 'deposit_receipt', 'bank_account_id']));
            }

            auth()->user()->orderStatuses()->firstOrCreate([
                'order_id' => $order->id,
                'status_id' => $order->status_id,
                'type' => 'order',
                'reason' => isset($data['reason']) ? $data['reason'] : null
            ]);
            if($order->paymentMethod->type=='online'){
                $payment=$this->paymentService->doPayment($order,$data['online_payment_method_id'],$data['payment_type']??'mobile');
            }
            $order = Order::withoutGlobalScopes()->with('orderService', 'store', 'user', 'userAddress')->find($order->id);
            if (Storage::disk('public')->missing('invoices')) {
                Storage::disk('public')->makeDirectory('invoices');
            }
            // load invoice pdf file
            MPDF::loadView('bill', ['order' => $order], [], [])->save(storage_path("app/public/invoices/".$data['invoice_file']));

            // Commit Transaction
            DB::commit();
            if($order->paymentMethod->type=='online' && $payment){
                return new GenericPayload(
                    [
                        'payment_url' => $this->paymentService->paymentLink($payment),
                    ],
                    Response::HTTP_RESET_CONTENT
                );
            }
            $msg = __('success.orders.sentSuccesffuly'). ' '. $order->id;
            $admin = Admin::whereIsActive(1)->first();
            $notif_data = array(
                'ar' => ['title' => 'طلب جديد', 'body' => 'تم اضافة طلب جديد ورقم الطلب ه '. $order->id],
                'en' => ['title' => 'new order', 'body' => 'order No.'.$order->id. 'has been added'],
            );
            $admin->notify(new OrderNotification($order, $notif_data));
            send_fcm_notification(
                $admin,
                [
                    "title" => __('general.orders.newOrder'),
                    "body" => $msg,
                    "type" => 'service_order',
                    "model_id" =>  $order->id
                ],
                true
            );
            return new GenericPayload(
                [
                    'message' => $msg,
                    'order' => new OrderResource($order),
                ],
                Response::HTTP_RESET_CONTENT
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (\Illuminate\Database\QueryException $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        } catch (\PDOException $ex){
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        }
        catch (\Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                $ex->getMessage(), 422
            );

        }
    }

    private function getCouponDiscount($coupon, $total = 0.000){
        $discount = 0.00;
        //$discount_percent = 0;
        if ($coupon->type == "value"){
            $discount = $total > 0 ? ($coupon->value <= $total ? $coupon->value : $total) : 0.00;
            //$discount_percent = $total > 0 ? ($coupon->value <= $total ? round(($coupon->value/$total)*100, 3) : 100) : 0;

        }else if ($coupon->type == 'percentage'){
            $discount = $total * $coupon->value / 100;
            //$discount_percent = $coupon->value;
        }else{
            $discount = 0.00;
            //$discount_percent = 0;
        }
        return $discount;
    }

    private function validateCoupon($promo_code, $total, $store_id, $user_id) {
        $msg = null;
        $promo_code_obj = null;
        $promo_code_discount = 0.000;
        $promo_code_obj = PromoCode::where([
            ['code', $promo_code],
            ['is_active', 1],
            ['start_date', '<=', date('Y-m-d')],
            ['end_date', '>=', date('Y-m-d')]
        ])
        ->whereRelation('stores', 'stores.id', $store_id)
        // ->whereHas('stores', function ($query) use($store_id){
        //     $query->where('stores.id', $store_id);
        // })
        ->first();
        if(!$promo_code_obj)
            return ['msg' => __('error.coupons.invalidCoupon')];

        if($promo_code_obj->applied_to == 'specific_user' && $promo_code_obj->user_id != $user_id)
            return ['msg' => __('error.coupons.invalidCoupon')];

        $previous_orders_count = $promo_code_obj->orders->where('promo_code_id', $promo_code_obj->id)->count();
        if($previous_orders_count >= $promo_code_obj->max_use_number)
            $msg = __('error.coupons.maxUsageNo');
        if($total < $promo_code_obj->order_min_cost)
            $msg = __('error.coupons.lowOrderCost');

        return [
            'promo_code' => $promo_code_obj,
            'coupon_discount' => $this->getCouponDiscount($promo_code_obj, $total),
            'msg' => $msg
        ];

    }

    private function handleOrderItems($order, $service, $data) {
        $err_msg = null;
        //check and save order items
        $order_item = $order->orderService()->create([
            'service_id'  => $service->id,
            'service_price' => $service->price,
            'preview_fees' => $service->preview_fees,
            'subcategory_id' => $data['subcategory_id'],
            'problem_description' => $data['problem_description']
        ]);
        return null;
    }
}
