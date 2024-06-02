<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Models\State;
use Symfony\Component\HttpFoundation\Response;

class ShowStateService extends Service
{
    public function handle($data = [])
    {
        $state = State::findOrFail($data['state_id']);
        try {
            return new GenericPayload($state, Response::HTTP_CREATED);
        }  catch (\Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
