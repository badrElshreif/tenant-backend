<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use Illuminate\Support\Arr;
use App\Tenant\Location\Domain\Models\State;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ToggleStateStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $state = State::findOrFail($data['state_id']);

            if($state->is_active){
                if(count($state->cities()->where('is_active',1)->get()) > 0)
                    return new GenericPayload(
                        __('error.cannotDeactivate'), 422
                    );

                if(count($state->addresses()->get()) > 0)
                    return new GenericPayload(
                        __('error.cannotDeactivate'), 422
                    );
            }


            $state->cities()->update([
                'is_active' => !$state->is_active
            ]);

        	$state->update([
                'is_active' => !$state->is_active
            ]);

            return new GenericPayload($state, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
