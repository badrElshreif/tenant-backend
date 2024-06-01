<?php

namespace App\Tenant\Order\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User\Domain\Resources\UserLiteResource;
use Illuminate\Support\Str;

class BankTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $file_type = Str::endsWith($this->deposit_receipt, ['.png', '.jpg', '.jpeg']) ? 'image': 'pdf';

        return [
            'id' => $this->id,
            'depositor_name' => $this->depositor_name,
            'deposit_amount' => $this->deposit_amount,
            'deposit_receipt' => $this->deposit_receipt,
            'file_type' => $file_type
        ];
    }
}
