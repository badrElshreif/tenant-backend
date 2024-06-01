<?php

namespace App\Tenant\Admin\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Payloads\UnauthorizedPayload;
use App\Infrastructure\Domain\Services\Service;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Tenant\Admin\Domain\Resources\AdminResource;
use App\Tenant\Admin\Domain\Resources\AdminLiteResource;
use Illuminate\Support\Facades\Hash;

class LoginAdminService extends Service
{
    public function __construct()
    {
        Auth::setDefaultDriver('admins');
        Auth::shouldUse('admin');
    }

    public function handle($data = [])
    {

        try {
            $rememberMe = boolval(Arr::has($data, "remember_me") ? Arr::get($data, "remember_me") : false);

            $credentials = Arr::only($data, ["email", "password"]);
            $admin = Admin::where('email', $data['email'])->first();
            //if(auth('tenant-admin')->attempt($credentials)){
            if ($admin && Hash::check($data['password'], $admin->password)) {

                if (!$admin->is_active) {
                    return new GenericPayload(__('error.inActiveUser'), 425
                    );
                }

               // dd($admin);
                $token = $admin->createToken('tenant-admin')->accessToken;
                // if (!Auth::user('admin')->hasRole('super admin')) {
                //     // auth("admin")->logout();
                //     return new GenericPayload( __('error.wrongLoginData'), 425
                //     );
                // }
                //$this->incrementLoginNum();
                if (isset($data['device_token'])) {
                    $admin->tokens()->firstOrCreate([
                        'device_token' => $data['device_token']
                    ]);
                }

                return new GenericPayload($this->respondWithToken($token));
            }

            //return new GenericPayload( __('error.wrongPassword'), 422);
            return new GenericPayload(__('error.wrongLoginData'), 422);

        } catch (\Exception $ex) {
            dd($ex->getMessage() . " " . $ex->getFile() . " " . $ex->getLine());
            return new GenericPayload(
                ('error.someThingWrong'), 422
            );
        }
    }

    private function incrementLoginNum()
    {
        auth('tenant-admin')->user()->increment('login_numbers', 1, ['last_login_at' => Carbon::now()]);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            //  'expires_in' => auth()->factory()->getTTL() * 60,
        //    'user' => new AdminLiteResource(auth('tenant-admin')->user())
        ];
    }
}
