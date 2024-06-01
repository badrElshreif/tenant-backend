<?php

namespace App\Tenant\Order\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Infrastructure\Domain\Resources\GenericNameResource;
use Illuminate\Support\Str;

class PaymentLiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $file_type = Str::endsWith($this->receipt, ['.png', '.jpg', '.jpeg']) ? 'image': 'pdf';
        return [
            'id' => $this->id,
            'store' => new GenericNameResource($this->store),
            'total' => number_format((float)$this->total, 2, '.', ''),
            'application_dues_percentage' => $this->application_dues_percentage,
            'application_dues' => number_format((float)$this->application_dues, 2, '.', ''),
            'amount' => number_format((float)$this->amount, 2, '.', ''),
            'receipt' => $this->receipt,
            'file_type' => $file_type,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y')
        ];
    }
}
