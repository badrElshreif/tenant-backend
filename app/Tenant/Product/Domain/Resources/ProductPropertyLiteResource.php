<?php

namespace App\Tenant\Product\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Property\Domain\Resources\PropertyLiteResource;
use App\Property\Domain\Resources\PropertyOptionResource;
use App\Infrastructure\Domain\Resources\GenericNameResource;

class ProductPropertyLiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $property = \App\Property\Domain\Models\Property::find($this->pivot->property_id);
        $value = $this->pivot->value;
        $option_value = $this->pivot->value;

        if(!$this->pivot->value){
            $option = \App\Property\Domain\Models\PropertyOption::find($this->pivot->property_option_id);
            if($option){
                $option_value = new GenericNameResource($option);
                $value = $option_value ? $option_value->name : '';
            }
        }
        return [
            'id' => $this->id,
            'property' => new GenericNameResource($property),
            'value' => $value,
            'option_value' => $option_value
        ];
    }
}
