<?php

namespace App\Infrastructure\Http\Middleware;

use Closure;

class TenantExpireToken
{

    public function handle($request, Closure $next)
    {
        if (auth('tenant-admin')->check()) {
            $token = auth('tenant-admin')->user()->token();
            if ($token->expires_at < now()) {
                $token->revoke();
                return response()->json(['status' => false,
                    'message' => 'Token has expired'], 401);
            }
        }
        return $next($request);
    }
}
