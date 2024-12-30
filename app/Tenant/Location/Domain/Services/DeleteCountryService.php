<?php

namespace App\Tenant\Location\Domain\Services;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Models\Country;
use Symfony\Component\HttpFoundation\Response;

class DeleteCountryService extends Service
{
    public function handle($data = [])
    {
        try {
            $country = Country::findOrFail($data['country_id']);
            if (count($country->states()->where('is_active', 1)->get()) > 0)
                return [
                    'status' => false,
                    'message' => __('error.cannotDelete'),
                    'code' => Response::HTTP_NO_CONTENT,
                ];

            if (count($country->addresses()->get()) > 0)
                return [
                    'status' => false,
                    'message' => __('error.cannotDelete'),
                    'code' => Response::HTTP_NO_CONTENT,
                ];

            $country->forceDelete();
            return [
                'status' => true,
                'message' => __('success.deletedSuccessfuly'),
                'code' => Response::HTTP_NO_CONTENT,
            ];
        } catch (\Exception $ex) {
            return [
                'status' => false,
                'message' => $ex->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }
    }
}
