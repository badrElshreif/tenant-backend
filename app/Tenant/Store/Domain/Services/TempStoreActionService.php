<?php

namespace App\Tenant\Store\Domain\Services;

use App\Admin\Domain\Models\Admin;
use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Store\Domain\Models\Store;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Tenant\Store\Domain\Models\StoreTemp;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class TempStoreActionService extends Service
{
    public function handle($data = [])
    {
        try {
            if ($data['status'] == 'accept') {
                $store = Store::findOrFail($data['store_id']);
                $storeTemp = StoreTemp::where('store_id', $data['store_id'])->first();
                $ar = $storeTemp->translate('ar')->toArray();
                $en = $storeTemp->translate('en')->toArray();
                $updateData = $storeTemp->toArray();
                $updateData['ar'] = $ar;
                $updateData['en'] = $en;
                $adminData = Arr::only($updateData, ['phone', 'email']);
                if ($updateData['name2']) {
                    $adminData['name'] = $updateData['name2'];
                }
                $store->update($updateData);
                Admin::where('store_id', $data['store_id'])->update($adminData);
            }
            StoreTemp::where('store_id', $data['store_id'])->delete();
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
