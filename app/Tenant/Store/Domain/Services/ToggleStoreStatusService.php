<?php

namespace App\Tenant\Store\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Store\Domain\Models\Store;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ToggleStoreStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $store = Store::findOrFail($data['store_id']);
            if($store->status != 1)
                return new GenericPayload(
                    __('error.notRegisteredStore'), 422
                );

            if($store->is_active == 1){
                if(!isset($data['deactivation_reason']))
                    return new GenericPayload(
                         __('error.requiredReason'), 422
                    );
            }
            $store->admins()->update([
                'is_active' => !$store->is_active
            ]);

            $store->update([
                'is_active' => !$store->is_active,
                'deactivation_reason' => $store->is_active == 1 ? $data['deactivation_reason'] : null
            ]);

            return new GenericPayload($store, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
