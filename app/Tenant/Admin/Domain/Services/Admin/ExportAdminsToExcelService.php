<?php

namespace App\Tenant\Admin\Domain\Services\Admin;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Tenant\Admin\Domain\Filters\AdminFilter;
use App\Tenant\Admin\Domain\Exports\AdminsExport;
use Excel;
use Symfony\Component\HttpFoundation\Response;

class ExportAdminsToExcelService extends Service
{
    protected $admin, $filter;

    public function __construct(Admin $admin, AdminFilter $filter)
    {
        $this->admin = $admin;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
//        $store_id = null;
        if(auth()->guard('store')->check() || auth()->guard('center')->check())
            $store_count = auth()->user()->stores()->count();
//        $this->admin = $this->admin
//            ->when($store_id, function($collection) use ($store_id){
//                return $collection->whereNotNull('store_id')->where('store_id', $store_id);
//            }, function ($collection) {
//                return $collection->whereNull('store_id');
//            });

        if ($store_count){
            $this->admin = $this->admin->whereHas('stores');
        }else{
            $this->admin = $this->admin->whereDoesntHave('stores');
        }

//        $this->admin = Admin::query()

        return new GenericPayload(
        	Excel::download(new AdminsExport($this->admin, $this->filter), 'admins.xlsx'), Response::HTTP_RESET_CONTENT
        );
    }
}


