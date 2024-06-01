<?php

namespace App\Tenant\Store\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Store\Domain\Models\Store;
use App\Tenant\Store\Domain\Models\StoreTemp;
use Symfony\Component\HttpFoundation\Response;

class GetBankDataService extends Service
{
    public function handle($data = [])
    {

            $admin = auth('store')->user();

            $store = Store::where('seller_id', $admin->id)->first();

            return new GenericPayload($store, Response::HTTP_CREATED);

    }
}
