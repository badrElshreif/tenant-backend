<?php

namespace App\Tenant\Admin\Domain\Services\Admin;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Store\Domain\Models\StoreAdmin;
use App\Store\Domain\Models\CenterAdmin;
use App\Infrastructure\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class GetAdminService extends Service
{
    public function handle($data = [])
    {
        try {
            //$admin = Admin::findOrFail($data['admin_id']);
            if(auth()->guard('store')->check() || auth()->guard('center')->check()){
                if(auth()->user()->store->type == 'stores')
                    $admin = StoreAdmin::findOrFail($data['admin_id']);
                else
                    $admin = CenterAdmin::findOrFail($data['admin_id']);
            }else {
                $admin = Admin::findOrFail($data['admin_id']);
            }

            return new GenericPayload($admin, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                ('error.someThingWrong'), 422
            );
        }


    }
}
