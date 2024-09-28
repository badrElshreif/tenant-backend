<?php

namespace App\Tenant\Order\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User\Domain\Resources\UserLiteResource;
use App\Refund\Domain\Resources\RefundResource;
use App\User\Domain\Resources\UserAddressResource;
use App\PromoCode\Domain\Resources\PromoCodeLiteResource;
use App\Infrastructure\Domain\Resources\GenericNameResource;
use App\Tenant\AppContent\Domain\Models\Setting;

class FinancialDuesResource extends JsonResource
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

        $date = $this->created_at->addDays($refund_period);

        $resource =  [
            'id' => $this->id,
            'application_dues' => $this->application_dues > 0 ?number_format((float)$this->application_dues, 2, '.', '') : 0.00,
            'application_dues_percentage' => $this->application_dues_percentage > 0 ?number_format((float)$this->application_dues_percentage, 2, '.', '') : 0.00,
            'total' => $this->total > 0 ?number_format((float)$this->total, 2, '.', '') : 0.00,
            'order_date' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            'due_date' => \Carbon\Carbon::parse($date)->translatedFormat('d M Y'),
            'due_time' => \Carbon\Carbon::parse($date)->translatedFormat('H:i'),
            'store' => new GenericNameResource($this->store),
        ];

        return $resource;
    }
}
