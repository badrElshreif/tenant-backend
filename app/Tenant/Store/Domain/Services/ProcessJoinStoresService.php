<?php

namespace App\Tenant\Store\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Tenant\Store\Domain\Models\Store;
use App\Tenant\Store\Domain\Filters\StoreFilter;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;

class ProcessJoinStoresService extends Service
{

    public function handle($data = [])
    {
        try {
            $store = Store::findOrFail($data['store_id']);
            $newStatus=$data['status']==1?1:0;
            /*if($newStatus == 0){
                if(!isset($data['rejection_reason']))
                    return new GenericPayload(
                        __('error.requiredReason'), 422
                    );
            }*/
            $store->admins()->update([
                'is_active' => $newStatus
            ]);

            $store->update([
                'is_active' => $newStatus,
                'status'    => $newStatus,
                'rejection_reason' => $data['rejection_reason']??null
            ]);
        // Mail::to('abanoubsamir@fudex.com.sa')->send(new \App\Tenant\Store\Domain\Mails\StoreAdminAcceptedEmail());
        Mail::to($store->email)->send(new \App\Tenant\Store\Domain\Mails\StoreAdminAcceptedEmail());
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
