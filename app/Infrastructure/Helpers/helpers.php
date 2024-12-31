<?php

use App\Infrastructure\Domain\Payloads\GenericPayload;
use Illuminate\Support\Str;
use App\Tenant\AppContent\Domain\Models\Setting;

if (!function_exists('getSubdomain')) {
    function getSubdomain(): ?string
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $url = parse_url($url);
            $exploded = explode('.', $url['host']);

            if (count($exploded) > 1) {
                return $exploded[0];
            }
        }
        return null;
    }
}

if (!function_exists('getDomain')) {
    function getDomain(): string
    {
        $host = parse_url(url('/'), PHP_URL_HOST);
        $parts = explode('.', $host);
        if (count($parts) == 2 && $parts[1] == 'localhost') {
            return 'localhost';
        }
        if (count($parts) > 2) {
            $domain = implode('.', array_slice($parts, -2));
        } else {
            $domain = $host;
        }
        return $domain;
    }
}

function getTenant()
{
    return app(\App\Main\Tenant\Domain\Models\Tenant::class);
}

function routeTenant($routeName, $parameters = null)
{
    if (request()->headers->has('tenant')) {
//        return "http://" . getTenant()->slug . "." . request()->getHost() .
//            ltrim(route($routeName, $parameters, false), getTenant()->slug);
    }
    if (!request()->headers->has('tenant')) {
        $parameters['tenant'] = getTenant()->slug;
    }

    return route($routeName, $parameters);
}

if (!function_exists('responseApi')) {
    function responseApi($responseType, $data, $statusCode = 200, $resource = null)
    {
        return (new \App\Infrastructure\Responders\ResponderX())->getApiResponse($responseType, $data, $statusCode, $resource);
        // return new GenericPayload($data, $statusCode, $responseType);
    }
}

if (!function_exists('responseView')) {
    function responseView($viewPath, $data, $statusCode = 200, $resource = null)
    {
        return (new \App\Infrastructure\Responders\ResponderX())->getViewResponse($viewPath, $data);
    }
}

function setting($key = null)
{
    if (is_null($key)) {
        return (Setting::where('key', 'added_tax')->first())?->body;
    }
    return (Setting::where('key', $key)->first())?->body;
}
