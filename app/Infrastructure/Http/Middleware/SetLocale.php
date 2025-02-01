<?php

namespace App\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{

    public function handle(Request $request, Closure $next): Response
    {

        $locale = $request->getPreferredLanguage(config('app.available_locales'));
        if ($locale) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
