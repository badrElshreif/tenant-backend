<?php

namespace App\Infrastructure\Http\Middleware;

use Closure;

class RestrictTelescopeAccess
{
    public function handle($request, Closure $next)
    {
        if (config('telescope.enabled')) {
            return $next($request);
        }

        abort(403, 'Not allowed');
    }
}
