<?php

namespace App\Tenant\Order\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OnlinePaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' =>$this->PaymentMethodId?? $this['PaymentMethodId'],
            'ar' => $this->PaymentMethodAr??$this['PaymentMethodAr'],
            'en' => $this->PaymentMethodEn??$this['PaymentMethodEn'],
            'image' =>$this->ImageUrl?? $this['ImageUrl'],
        ];
    }
}
