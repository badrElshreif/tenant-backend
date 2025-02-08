<?php

namespace App\User\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User\Domain\Resources\UserAddressResource;

class UserCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge([
            'id' => $this->id,
            'name' => $this->name,
            'card_number' => $this->card_number,
            'code' => $this->code,
            'expired_at' => $this->expired_at,
        ]);
    }
}
