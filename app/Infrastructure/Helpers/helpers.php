<?php

use App\Infrastructure\Domain\Payloads\GenericPayload;
use Illuminate\Support\Str;

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
        $parsed = parse_url(url('/'));
        $exploded = explode('.', $parsed["host"]);
        if (count($exploded) > 2) {
            return Str::replaceLast($exploded[0] . ".", "", url('/'));
        } else {
            return url('/');
        }
    }
}

function getTenant()
{
    return app(\App\Main\Tenant\Domain\Models\Tenant::class);
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
