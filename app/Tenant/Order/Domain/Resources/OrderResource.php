<?php

namespace App\Tenant\Order\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User\Domain\Resources\UserLiteResource;
use App\Refund\Domain\Resources\RefundResource;
use App\User\Domain\Resources\UserAddressResource;
use App\PromoCode\Domain\Resources\PromoCodeLiteResource;
use App\Infrastructure\Domain\Resources\GenericNameResource;
use App\AppContent\Domain\Models\Setting;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //`user_id`, `coupon_id`, `subtotal`, `total`, `tax`, `coupon_name`, `coupon_discount_percentage`, `status_id`, `user_address_id`, `address`, `delivery_charge`, `shipping_company_id`, `notes`, `is_gift`
        $setting = Setting::where('key', 'refund_period')->first();
        $refund_period = $setting ? $setting->body : 10;
        $to = \Carbon\Carbon::now();
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $this->created_at);
        $diff_in_days = $to->diffInDays($from);
        $can_refund= false;
        // if($this->status->key != 'new' && $this->status->key != 'canceled' && $this->status->key != 'rejected' && $diff_in_days <= $refund_period && $this->refund == null)
        if($this->status->key == 'delivered' && $diff_in_days <= $refund_period && $this->refund == null)
            $can_refund = true;

        $bank_transfer = null;
        if(request()->isMethod('get') && isset(request()->id) || request()->isMethod('post')){
            $bank_transfer =  new BankTransferResource($this->bankTransfer);
        }
        $resource =  [
            'id' => $this->id,
            'user' => new UserLiteResource($this->user),
            'promo_code' => new PromoCodeLiteResource($this->promoCode),
            'status' => new GenericNameResource($this->status),
            'payment_method' => $this->paymentMethod ? new GenericNameResource($this->paymentMethod) : null,
            'user_address' => new UserAddressResource($this->userAddress),
            'order_added_tax' => number_format((float)$this->order_added_tax, 2, '.', ''),
            'subtotal' => number_format((float)($this->subtotal - $this->offer_discount), 2, '.', ''),
            'promo_code_discount' => $this->promo_code_discount > 0 ?number_format((float)$this->promo_code_discount, 2, '.', '') : 0.00,
            'total' => $this->total > 0 ?number_format((float)$this->total, 2, '.', '') : 0.00,
            'wallet_payout' => number_format((float)$this->wallet_payout, 2, '.', ''),
            'remain_after_wallet_use' => number_format((float)$this->remain, 2, '.', ''),
            'notes' => $this->notes,
            'bank_transfer' => $bank_transfer,
            'invoice' => $this->invoice_file,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            'statuses' => OrderStatusResource::collection($this->statuses),
            'admin_notes' => $this->admin_notes,
            'store' => new GenericNameResource($this->store),
        ];

        if($this->type == 'stores'){
                //$resource['status'] = new GenericNameResource($this->status);
                $resource['can_refund'] = $can_refund;
                $resource['delivery_charge'] = number_format((float)$this->delivery_charge, 2, '.', '');
                $resource['offer_discount'] = number_format((float)$this->offer_discount, 2, '.', '');
                $resource['warranties_amount'] = number_format((float)$this->warranties_amount, 2, '.', '');
                $resource['items'] = OrderItemResource::collection($this->orderItems);
                $resource['refund'] = ($this->refund && $this->refund->status->key == 'accepted') ? new RefundResource($this->refund) : null;
                $resource['refund_status'] = optional(optional($this->refund)->status)->key;
        }

        if($this->type == 'centers'){
            $resource['item'] = new OrderServiceResource($this->orderService);
            $resource['service_wanted_date'] = $this->service_wanted_date;
        }

        return $resource;
    }
}
