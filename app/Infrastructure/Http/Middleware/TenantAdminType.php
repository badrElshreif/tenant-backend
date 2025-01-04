<?php

namespace App\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TenantAdminType
{

    public function handle($request, Closure $next, $guard = 'tenant-admin')
    {

        $user = auth()->user();
        if ($guard === 'tenant-admin' && $user->type !== 'admin') {
            abort(403, 'Unauthorized');
        }

        if ($guard === 'tenant-store' && $user->type !== 'store') {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
