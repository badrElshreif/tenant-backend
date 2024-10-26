<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Location\Domain\Models\Country;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Tenant\Location\Domain\Resources\CountryResource;
use Symfony\Component\HttpFoundation\Response;

class ToggleCountryStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $country = Country::findOrFail($data['country_id']);

            if($country->is_active){
                if(count($country->states()->where('is_active',1)->get()) > 0)
                    return new GenericPayload(
                        __('error.cannotDeactivate'), 422
                    );

                if(count($country->addresses()->get()) > 0)
                    return new GenericPayload(
                        __('error.cannotDeactivate'), 422
                    );
            }

            // $country->states()->update([
            //     'is_active' => !$country->is_active
            // ]);
            foreach ($country->states as $state ) {
                $state->update([
                    'is_active' => !$country->is_active
                ]);

                $state->cities()->update([
                    'is_active' => !$country->is_active
                ]);
            }

        	$country->update([
                'is_active' => !$country->is_active
            ]);

            return new GenericPayload($country,
                Response::HTTP_OK,
                ResponseType::SingleResource,
                CountryResource::class,
            );
            //return new GenericPayload($country, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                 __('error.someThingWrong'), 422
            );
        }

    }
}
