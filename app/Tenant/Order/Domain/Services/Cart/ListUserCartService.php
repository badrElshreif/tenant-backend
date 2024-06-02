<?php

namespace App\Tenant\Order\Domain\Services\Cart;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Cart;
use App\AppContent\Domain\Models\Setting;
use Symfony\Component\HttpFoundation\Response;
use App\Tenant\Order\Domain\Resources\CartResource;
use App\Infrastructure\Traits\ApiPaginator;

class ListUserCartService extends Service
{
    use ApiPaginator;
    public function handle($data = [])
    {
        try {
            $setting = Setting::where('key', 'cart_products_max_period')->first();
            $cart_period = $setting ? $setting->body : 20;
            $to = \Carbon\Carbon::now();
            $limit = isset($data['per_page']) ? $data['per_page'] : config('app.pagination_limit');
            $cart = Cart::whereDeviceId($data['device_id'])->orWhere('user_id',auth()->id());

            /*if(auth()->check()){
                $cart = auth()->user()->cart();
            }else{
                $cart = Cart::whereDeviceId($data['device_id']);
            }*/
            $total = 0.00;
            $warranties_total = 0.00;
            foreach ($cart->get() as $item) {
                // $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $item->created_at);
                // $diff_in_days = $to->diffInDays($from);
                // if($diff_in_days > $cart_period){
                //     $item->delete();
                // }else{
                //     $total = $total + ($item->quantity *$this->getDiscount($item->product));
                // }
                $total = $total + ($item->quantity *$this->getDiscount($item->product));
                if($item->warranty)
                    $warranties_total = $warranties_total + ($item->quantity *$item->warranty->price);
            }
            // foreach ($cart->get() as $item) {
            //     $total = $total + ($item->quantity *$this->getDiscount($item->product));
            // }
            $cart_list = $cart->paginate($limit);
            return new GenericPayload([
                'subtotal' => number_format((float)$total, 2, '.', ''),
                'warranties_total' => number_format((float)$warranties_total, 2, '.', ''),
                'cart' => $this->getPaginatedResponse(
                    $cart_list,
                    CartResource::collection($cart_list)
                )
            ], Response::HTTP_RESET_CONTENT);

        } catch (\PDOException $ex){
            return new GenericPayload($ex->getMessage(), 422);
        } catch (Exception $ex) {
            return new GenericPayload($ex->getMessage(), 422);
        }
    }

    private function getDiscount($product){
        if($product){
            $discount = 0.00;
            $free_product = null;
            $offer = $product->offer->first();
            if ($offer){
                if ($offer->type == "free_product"){
                    $free_product = $offer->freeProduct;
                }else if ($offer->type == 'percentage'){
                    $discount = $product->price_including_tax * $offer->value / 100;
                }else{
                    $discount = $offer->value;
                }
            }
            return $product->price_including_tax - $discount;
        }
        return 0;
    }
}
