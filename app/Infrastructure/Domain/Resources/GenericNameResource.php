<?php

namespace App\Infrastructure\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GenericNameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $response = [
            'id'   => $this->id,
            'name' => $this->name
        ];

        if ($this->key)
        {
            $response['key'] = $this->key;
        }

        if ($this->type)
        {
            $response['type'] = $this->type;
        }

        if ($this->logo)
        {
            $response['logo'] = $this->logo;
        }

        if ($this->average_rate)
        {
            $response['rate'] = $this->average_rate;
        }

        if ($this->price > 0)
        {
            $response['price'] = $this->price;
        }

        if ($this->wallet_balance)
        {
            $response['wallet_balance'] = $this->wallet_balance;
        }

        if ($this->property_type_id)
        {
            $response['property_type'] = $this->propertyType->key;
        }

        if ($this->status)
        {
            $response['status'] = $this->status;
        }

        return $response;
    }

}
