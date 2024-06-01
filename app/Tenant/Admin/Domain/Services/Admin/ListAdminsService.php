<?php

namespace App\Tenant\Admin\Domain\Services\Admin;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Tenant\Admin\Domain\Filters\AdminFilter;
use Symfony\Component\HttpFoundation\Response;
use App\Store\Domain\Models\StoreAdmin;
use App\Store\Domain\Models\CenterAdmin;

class ListAdminsService extends Service
{
    protected $admin, $filter;

    public function __construct(AdminFilter $filter)
    {
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        //$store_id = null;
        // if(auth()->guard('store')->check() || auth()->guard('center')->check()){

        $store_id = auth()->user()->store_id;

        if(auth()->guard('store')->check()){
            $admin = StoreAdmin::where('store_id', $store_id);
        }else if(auth()->guard('center')->check()){
            $admin = CenterAdmin::where('store_id', $store_id);
        }else {
//            $admin = Admin::whereNull('store_id');
            $admin = Admin::where('is_seller',false);
        }

        $order = $data['orderBy'] ? : 'id';
        $order_type = $data['orderType'] ? : 'ASC';
        $admins = $admin
        // ->when($store_id, function($collection) use ($store_id){
        //     return $collection->where('admins.store_id', $store_id);
        // }, function ($collection) {
        //     return $collection->whereNull('store_id');
        // })
        // ->whereHas('roles', function($q) {
        //     $q->whereNotIn('name', ['super-admin', 'super admin']);
        // })
        ->filter($this->filter)->orderBy($order, $order_type)->paginate(config('app.pagination_limit'));

        return new GenericPayload($admins, Response::HTTP_ACCEPTED);
    }
}
