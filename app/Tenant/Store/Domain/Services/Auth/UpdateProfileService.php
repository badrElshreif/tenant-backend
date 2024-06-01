<?php

namespace App\Tenant\Store\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Admin\Domain\Models\Admin;
use App\Infrastructure\Exceptions\UserNotFoundException;
use App\Tenant\Store\Domain\Models\StoreTemp;
use Illuminate\Support\Arr;

class UpdateProfileService extends Service
{
    public function handle($data = [])
    {
        try {
            $admin = auth()->user();
            if ($admin->store) {
                if ($storeTemp = StoreTemp::where('store_id', $admin->store_id)->first()) {
                    $updataData=Arr::only($data, ["email", "phone", "image", "ar", "en"]);
                    $updataData['name2']=$data["name"];
                    $storeTemp->update($updataData);
                } else {
                    $storeData = $admin->store->toArray();
                    $storeData['store_id'] = $admin->store_id;
                    $storeTemp = StoreTemp::create($storeData);
                    $updataData=Arr::only($data, ["email", "phone", "image", "ar", "en"]);
                    $updataData['name2']=$data["name"];
                    $storeTemp->update($updataData);
                }
            } else {
                $admin->update($data);
            }
//            if ($admin->store && isset($data['avatar'])) {
//                $admin->store->update(Arr::only($data, ["email", "phone", "image", "ar", "en"]));
//            }


        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
        return new GenericPayload($admin);
    }
}
