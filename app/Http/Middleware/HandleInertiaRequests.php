<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{



    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

       // JsonResource::withoutWrapping();
        app()->setLocale("ar");
        return array_merge(parent::share($request), [
        //    'routeTenant' => fn($name="", $parameters = []) => routeTenant($name, $parameters),
            'auth' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'avatar' => $request->user()->avatar,
            ] : null,
            'flash' => [
                'success' => $request->session()->get('success')
            ],
            'locale' => app()->getLocale(),
            //'auth_permissions'=>$request->user() ?  $request->user()->getPermissionsViaRoles()->pluck('name')->toArray() : []
        ]);
    }


}
