<?php

namespace App\Tenant\Store\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Store\Domain\Models\Store;
use App\Tenant\Store\Domain\Resources\StoreResource;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Tenant\Store\Domain\Events\StoreAcceptedEvent;
use App\Tenant\Store\Domain\Events\StoreRejectedEvent;
use Symfony\Component\HttpFoundation\Response;
use App\Admin\Domain\Models\Admin;
use App\Admin\Domain\Models\Role;
use App\Tenant\Store\Domain\Models\StoreAdmin;
use App\Tenant\Store\Domain\Models\CenterAdmin;

class UpdateStoreService extends Service
{
    public function handle($data = [])
    {

        try {
            // Begin Transaction
            DB::beginTransaction();
            $store = Store::findOrFail($data['store_id']);
            if(isset($data['is_active']) && $store->status != 1)
                return new GenericPayload(
                    __('error.notRegisteredStore'), 422
                );
            if(isset($data['status']) && $store->status != 0 && !isset($data['has_delivery_service']))
                return new GenericPayload(
                    __('error.statusChanged'), 422
                );

            // if(isset($data['status']) && $data['status'] == 1){
            //     $data['delivery_charge'] = setting('delivery_charge');
            //     $data['application_dues'] = setting('application_dues');
            // }

            $store->update($data);

            if(isset($data['status'])){
                if($data['status'] == 1){
                    $data['password'] = isset($data['password']) ? Hash::make($data['password']) : 'password';

                    if($store->type == 'stores'){
                        $admin = StoreAdmin::create([
                            'name' =>$store->username??$store->name,
                            //'name' => 'Adminstrator',
                            'email' => $store->email,
                            'phone' => $store->phone,
                            'password' => bcrypt($data['password']),
                            'is_active' => 1,
                            'store_id' => $store->id
                        ]);
                        $roles = Role::where('guard_name', 'store')->where('name', 'super admin')->first();
                    }
                    else{
                        $admin = CenterAdmin::create([
                            //'name' => 'Adminstrator',
                            'name' => $store->username??$store->name,
                            'email' => $store->email,
                            'phone' => $store->phone,
                            'password' => bcrypt($data['password']),
                            'is_active' => 1,
                            'store_id' => $store->id
                        ]);
                        $roles = Role::where('guard_name', 'center')->where('name', 'super admin')->first();
                    }
                    $admin->syncRoles($roles->id);
                    //dispatch(new \App\Jobs\SendAcceptedStoreEmailJob($store));
                  \Mail::to($store->email)->send(new \App\Tenant\Store\Domain\Mails\StoreAcceptedEmail($store->name, $admin->email, $data['password']));
                  $store->update([
                    'is_active' => 1
                  ]);
                }
                if($data['status'] == 2)
                    \Mail::to($store->email)->send(new \App\Tenant\Store\Domain\Mails\StoreRejectedEmail($store->name, $store->rejection_reason));
            }

            // Commit Transaction
            DB::commit();
            return new GenericPayload($store, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (\Illuminate\Database\QueryException $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
               $ex->getMessage(), 422
            );
        } catch (\PDOException $ex){
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
               $ex->getMessage(), 422
            );
        }
        catch (\Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
               $ex->getMessage(), 422
            );

        }
    }
}
