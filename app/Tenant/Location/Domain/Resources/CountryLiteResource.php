<?php

namespace App\Tenant\Location\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DB;
use App\Tenant\Location\Domain\Resources\StateResource;
class CountryLiteResource extends JsonResource
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
            "id" => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'flag' => $this->flag,
            'is_active' => $this->is_active,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->translatedFormat('d M Y'),
        ];
    }
}
