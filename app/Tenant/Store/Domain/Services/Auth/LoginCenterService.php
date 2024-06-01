<?php

namespace App\Tenant\Store\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Payloads\UnauthorizedPayload;
use App\Infrastructure\Domain\Services\Service;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use App\Admin\Domain\Models\Admin;
use App\Admin\Domain\Resources\AdminResource;
use App\Admin\Domain\Resources\AdminLiteResource;

class LoginCenterService extends Service
{
    public function __construct(){
        Auth::setDefaultDriver('center_admins');
        Auth::shouldUse('center');
    }
    public function handle($data = [])
    {
        try {
            $rememberMe = boolval(Arr::has($data, "remember_me") ? Arr::get($data, "remember_me") : false);
            $loginData = Arr::only($data, ["email", "password"]);
            if ($token = JWTAuth::attempt($loginData, $rememberMe)) {
               // dd(auth('store')->user()->id);
                if (!auth("center")->user()->store_id) {
                    //auth("store")->logout();
                    return new GenericPayload( __('error.wrongLoginData'), 425
                    );
                }

                if (auth("center")->user()->store->type != 'centers') {
                    //auth("center")->logout();
                    return new GenericPayload( __('error.wrongLoginData'), 425
                    );
                }

                if (!auth("center")->user()->is_active) {
                    //auth("center")->logout();
                    return new GenericPayload( __('error.inActiveUser'), 425
                    );
                }
                $this->incrementLoginNum();
                if(isset($data['device_token'])) {
                    auth("center")->user()->tokens()->firstOrCreate([
                        'device_token' => $data['device_token']
                    ]);
                }
                return new GenericPayload($this->respondWithToken($token));
            }

            //return new GenericPayload( __('error.wrongPassword'), 422);
            return new GenericPayload( __('error.wrongLoginData'), 422);

        } catch (ArgumentCountError $ex){
            return new GenericPayload(
                ('error.someThingWrong'), 422
            );
        } catch (Exception $ex) {
            return new GenericPayload(
                ('error.someThingWrong'), 422
            );
        }
    }

    private function incrementLoginNum()
    {
        auth()->user()->increment('login_numbers', 1, ['last_login_at' => Carbon::now()]);
    }

    /**
     * Get the token array
     structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => new AdminLiteResource(auth("center")->user())
        ];
    }
}
