<?php

namespace App\Tenant\Order\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User\Domain\Resources\UserLiteResource;
use App\User\Domain\Resources\UserAddressResource;
use App\AppContent\Domain\Models\Setting;
use App\Infrastructure\Domain\Resources\GenericNameResource;

class OrderLiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = [
            'id' => $this->id,
            'user' => new UserLiteResource($this->user),
            'promo_code' => new GenericNameResource($this->promoCode),
            'status' => new GenericNameResource($this->status),
            'payment_method' => $this->paymentMethod ? new GenericNameResource($this->paymentMethod) : null,
            'user_address' => new UserAddressResource($this->userAddress),
            'total' => $this->total,
            'application_dues' => $this->application_dues > 0 ?number_format((float)$this->application_dues, 2, '.', '') : 0.00,
            'application_dues_percentage' => $this->application_dues_percentage > 0 ?number_format((float)$this->application_dues_percentage, 2, '.', '') : 0.00,
            'amount_after_commission' => number_format((float)($this->total - $this->application_dues), 2, '.', ''),
            'notes' => $this->notes,
            'invoice' => $this->invoice_file,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
        ];

        if($this->type == 'centers'){
            $resource['item'] = new OrderServiceResource($this->orderService);
            $resource['service_wanted_date'] = $this->service_wanted_date;
        }

        return $resource;

    }
}
