<?php

namespace App\Main\Tenant\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;

use App\Infrastructure\Models\User;
use App\Main\Tenant\Domain\Events\TenantCreated;
use App\Main\Tenant\Domain\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CreateTenantService extends Service
{

    public function handle($data = [])
    {
        $request = (object)$data;
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $tenant = Tenant::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'default_lang' => 'en'
            ]);

            DB::commit();
            event(new TenantCreated($tenant, $user));
            return new GenericPayload($tenant, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
