<?php

namespace App\Tenant\Store\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Store\Domain\Models\Store;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Tenant\Store\Domain\Models\StoreTemp;
use Symfony\Component\HttpFoundation\Response;

class ShowStoreTempService extends Service
{
    public function handle($data = [])
    {
        try {
            $store = StoreTemp::findOrFail($data['store_id']);
            return new GenericPayload($store, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (\Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
