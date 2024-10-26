<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Enums\ResponseType;
use App\Tenant\Location\Domain\Resources\CountryResource;
use Illuminate\Support\Arr;
use App\Tenant\Location\Domain\Models\Country;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class UpdateCountryService extends Service
{
    public function handle($data = [])
    {
        try {
            $country = Country::findOrFail($data['country_id']);

        	$country->update($data);

            return new GenericPayload($country,
                Response::HTTP_OK,
                ResponseType::SingleResource,
                CountryResource::class,
            );
          //  return new GenericPayload($country, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }

    }
}
