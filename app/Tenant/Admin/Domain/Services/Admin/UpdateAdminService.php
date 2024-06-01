<?php

namespace App\Tenant\Admin\Domain\Services\Admin;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Store\Domain\Models\StoreAdmin;
use App\Store\Domain\Models\CenterAdmin;
use App\Infrastructure\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class UpdateAdminService extends Service
{
    public function handle($data = [])
    {
        try {
            if(isset($data['admin_id'])){
                $admin = Admin::findOrFail($data['admin_id']);
            }else{
                $admin = auth()->user();
            }

            if(isset($data['password'])){
                 $password = $data['password'];
                $data['password'] = Hash::make($data['password']);
            }

            $updated_data = Arr::except($data, ['roles', 'admin_id']);
            if(auth()->guard('store')->check()){
                $admin = StoreAdmin::findOrFail($admin->id);

            } else if(auth()->guard('center')->check()){
                $admin = CenterAdmin::findOrFail($admin->id);

            } else {
                $admin = Admin::findOrFail($admin->id);
            }

            $admin->update($updated_data);
            Mail::to($admin->email)->send(new \App\Tenant\Admin\Domain\Mail\editPasswordEmail($admin->name, $admin->email, $password));


            if(isset($data['roles']) && !($admin->hasRole('super admin') || $admin->hasRole('super-admin')) )
                $admin->syncRoles($data['roles']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $ex) {
            return new GenericPayload(
                __('error.invalidRole'), 422
            );
        } catch (\Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
        return new GenericPayload($admin, Response::HTTP_CREATED);
    }
}
