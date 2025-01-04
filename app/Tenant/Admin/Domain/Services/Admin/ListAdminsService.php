<?php

namespace App\Tenant\Admin\Domain\Services\Admin;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Tenant\Admin\Domain\Filters\AdminFilter;
use App\Tenant\Store\Domain\Models\CenterAdmin;
use App\Tenant\Store\Domain\Models\StoreAdmin;
use Symfony\Component\HttpFoundation\Response;

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

        // $store = auth()->user();
//        if (auth('tenant-store')->check()) {
//            $admin = StoreAdmin::where('store_id', $store->id);
//        }
//            $admin = Admin::whereNull('store_id');
        $admin = Admin::where('is_seller', 0);

        $order = $data['orderBy'] ?: 'id';
        $order_type = $data['orderType'] ?: 'ASC';
        $admins = $admin
            // ->when($store_id, function($collection) use ($store_id){
            //     return $collection->where('admins.store_id', $store_id);
            // }, function ($collection) {
            //     return $collection->whereNull('store_id');
            // })
            // ->whereHas('roles', function($q) {
            //     $q->whereNotIn('name', ['super-admin', 'super admin']);
            // })
            ->filter($this->filter)->orderBy($order, $order_type)
            ->paginate(config('app.pagination_limit'));

        return [
            'data' => $admins,
            'status' => true,
            'message' => __('success.foundSuccessfully'),
        ];
    }
}
