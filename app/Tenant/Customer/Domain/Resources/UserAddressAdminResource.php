<?php

namespace App\User\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Location\Domain\Resources\ConnectedCityResource;
use DB;

class UserAddressAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $db = config('database.connections.mysql.database');
        // $city = DB::table("{$db}.cities")->join("{$db}.city_translations", "{$db}.cities.id", '=', "{$db}.city_translations.city_id")->where("{$db}.cities.id", $this->city_id)->where("{$db}.city_translations.locale", app()->getLocale())->select("{$db}.cities.*", "{$db}.city_translations.name")->first();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'phone' => $this->phone,
            'is_primary' => $this->is_primary,
            'address' => $this->address,
            //'city_id' => $this->city_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'nearest_landmarks' => $this->nearest_landmarks,
            'city' => new ConnectedCityResource($this->city),
            //'country' => $this->country_id
        ];
    }
}
