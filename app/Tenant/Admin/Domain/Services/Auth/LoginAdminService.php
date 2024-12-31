<?php

namespace App\Tenant\Admin\Domain\Services\Auth;

use App\Infrastructure\Domain\Services\Service;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Tenant\Admin\Domain\Models\Admin;
use App\Tenant\Admin\Domain\Resources\AdminLiteResource;
use Illuminate\Support\Facades\Hash;

class LoginAdminService extends Service
{
    public function __construct()
    {

    }

    public function handle($data = [])
    {
        $rememberMe = boolval(Arr::has($data, "remember_me") ? Arr::get($data, "remember_me") : false);
        // $credentials = Arr::only($data, ["email", "password"]);
        try {
            $admin = Admin::where('email', $data['email'])->first();
            //if(auth('tenant-admin')->attempt($credentials)){
            if ($admin && Hash::check($data['password'], $admin->password)) {

                if (!$admin->is_active) {
                    return [
                        'status' => false,
                        'message' => __('error.inActiveUser'),
                        'code' => 425
                    ];
                }


                $this->incrementLoginNum($admin);
                if (isset($data['device_token'])) {
                    $admin->tokens()->firstOrCreate([
                        'device_token' => $data['device_token']
                    ]);
                }

                return [
                    'status' => true,
                    'data' => $this->respondWithToken($admin, $rememberMe),
                    'message' => __('success.login'),
                ];
            }

            return [
                'status' => false,
                'message' => __('error.wrongLoginData'),
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function incrementLoginNum($admin)
    {
        $admin->increment('login_numbers', 1, ['last_login_at' => Carbon::now()]);
    }


    protected function respondWithToken($admin, $rememberMe)
    {
        $tokenData = $admin->createToken('tenant-admin');
        $token = $tokenData->accessToken;
        if ($tokenData->token) {
            $tokenModel = $tokenData->token;
            $tokenModel->expires_at = $rememberMe
                ? Carbon::now()->addMonth()
                : Carbon::now()->addDays(7);
            $tokenModel->save();
        }

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => $tokenModel?->expires_at,
            'user' => new AdminLiteResource($admin)
        ];
    }
}
