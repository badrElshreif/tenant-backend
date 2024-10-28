<?php

namespace App\Infrastructure\Http\Middleware;

use App\Main\Tenant\Domain\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class TenantDatabaseConnection
{

    public function handle($request, Closure $next)
    {


        if (!empty($request->tenant) || $request->headers->has('tenant')) {
            $tenant = $request->tenant ?? $request->header('tenant') ?? "";
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();

            if ($request->headers->has('tenant')) {
                URL::forceRootUrl(($request->isSecure ? "https://" : "http://") . $tenant->slug . "." . $request->getHost());
//                Config::set('app.url', $tenant->slug . "." . $request->getHost());
            }

            //establish connection based on tenant (tenant_id)
            $database = "tenant_{$tenant->id}";

            config(["database.connections.tenant.database" => $database]);
            config(['database.default' => 'tenant']);
            config(["passport.connection" => 'tenant']);

            config(["telescope.storage.database.connection" => "tenant"]);
            config(["telescope.enabled" => true]);


            DB::purge('tenant');
            DB::reconnect('tenant');
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                abort(403, "Database connection failed");
            }
            app()->instance(Tenant::class, $tenant);
            $request->route()->forgetParameter('tenant');
            $response = $next($request);
            DB::disconnect("tenant");

            return $response;
        }


        return $next($request);
    }
}
