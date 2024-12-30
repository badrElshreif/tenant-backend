<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Models\Country;
use App\Tenant\Location\Domain\Resources\CountryResource;
use Symfony\Component\HttpFoundation\Response;

class ToggleCountryStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $country = Country::findOrFail($data['country_id']);

            if ($country->is_active) {
                if (count($country->states()->where('is_active', 1)->get()) > 0)
                    return [
                        'status' => false,
                        'message' => __('error.cannotDeactivate'),
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    ];

//                if (count($country->addresses()->get()) > 0)
//                    return new GenericPayload(
//                        __('error.cannotDeactivate'), 422
//                    );
            }

            // $country->states()->update([
            //     'is_active' => !$country->is_active
            // ]);
            foreach ($country->states as $state) {
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

            return [
                'status' => true,
                'data' => new CountryResource($country),
                'message' => __('success.updatedSuccessfuly'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }

    }
}
