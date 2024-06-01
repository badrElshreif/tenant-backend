<?php

namespace App\Tenant\Admin\Domain\Services\Admin;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Infrastructure\Exceptions\QueryException;
use Symfony\Component\HttpFoundation\Response;

class DeleteAdminService extends Service
{
    public function handle($data = [])
    {
        try {
            $admin = Admin::findOrFail($data['admin_id']);
            if(!($admin->hasRole('super admin') || $admin->hasRole('super-admin')))
                $admin->delete();

            return new GenericPayload(['message' => __('success.deletedSuccessfuly')], Response::HTTP_NO_CONTENT);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (\Exception $ex) {
            throw new QueryException;
        }

    }
}
