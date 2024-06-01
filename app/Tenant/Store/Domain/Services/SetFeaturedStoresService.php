<?php

namespace App\Tenant\Store\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Store\Domain\Models\Store;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetFeaturedStoresService extends Service
{
    public function handle($data = [])
    {

        try {
            // Begin Transaction
            DB::beginTransaction();
            if(isset($data['stores']) && count($data['stores']) > 0){
                $stores = Store::whereType($data['type'])->get();
                foreach ($stores as $store) {
                    $store->update(['is_featured' => 0]);
                }
                foreach ($data['stores'] as $store_id) {
                    $store = Store::findOrFail($store_id);
                    if($store->status != 1){
                        $msg = $store->type == 'stores' ? __('error.NotRegisterdStores') : __('error.NotRegisterdCenters');
                        return new GenericPayload(
                           $msg, 422
                        );
                    }
                    $store->update(['is_featured' => 1]);
                }
            } else {
                $stores = Store::whereType($data['type'])->get();
                foreach ($stores as $store) {

                    $store->update(['is_featured' => 0]);
                }
            }


            // Commit Transaction
            DB::commit();
            return new GenericPayload($store, Response::HTTP_NO_CONTENT);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (\Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
               $ex->getMessage(), 422
            );

        }
    }
}
