<?php

namespace App\Tenant\Store\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Store\Domain\Models\Store;
use App\Tenant\Store\Domain\Models\StoreAdmin;
use Illuminate\Support\Arr;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class CreateStoreService extends Service
{
    public function handle($data = [])
    {
        try {
            $is_admin=(Auth::check() && !Auth::user('admin')->is_seller && Auth::user('admin')->is_active)?1:0;
            $data['status'] = $is_admin;
            $data['is_active'] = $is_admin;
            if($is_admin)
                $data['type']='stores';
            if(str_starts_with($data['phone'], '9660')){
                $data['phone'] = substr($data['phone'],4,20);
                $data['phone'] = '966'.$data['phone'];
            }
            $sellerData=[
                'password'=>Hash::make($data['password']),
                'phone'=>$data['phone'],
                'email'=>$data['email'],
                'is_seller'=>1,
                'name'=>$data['username'],
                'is_active'=>1,//$data['is_active']
            ];
            $seller=StoreAdmin::create($sellerData);
            //unset($data['password']);
            $data['seller_id']=$seller->id;

            $store = Store::create($data);
//            return new GenericPayload($data, Response::HTTP_CREATED);
            return new GenericPayload($store, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\QueryException $ex) {
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        } catch (\PDOException $ex){
            return new GenericPayload(
                $ex->getMessage(), 422
            );
        }
        catch (\Exception $ex) {
            return new GenericPayload(
                $ex->getMessage(), 422
            );

        }
    }
}
