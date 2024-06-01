<?php

namespace App\Tenant\Order\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User\Domain\Resources\UserLiteResource;
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $type = explode("\\", $this->transactable_type);
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'transactable_type' => end($type),
            'transactable_id' => $this->transactable_id,
            'ar' => optional($this->translate('ar'))->only('reason'),
            'en' => optional($this->translate('en'))->only('reason'),
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
            'user' => new UserLiteResource($this->user),
        ];
    }
}
