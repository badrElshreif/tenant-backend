<?php

namespace App\User\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Payloads\UnauthorizedPayload;
use App\Infrastructure\Domain\Services\Service;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use App\User\Domain\Resources\UserResource;
use App\Order\Domain\Models\Cart;
use App\User\Domain\Models\Customer;
use Symfony\Component\HttpFoundation\Response;

class LoginUserService extends Service
{
    public function handle($data = [])
    {
        if(str_starts_with($data['phone'], '0')){
            $data['phone'] = substr($data['phone'],1,20);
        }

        $user = Customer::whereCountryCode($data['country_code'])->wherePhone($data['phone'])->first();
        if(!$user)
            return new GenericPayload( __('error.wrongLoginData'), 422);

        $rememberMe = boolval(Arr::has($data, "remember_me") ? Arr::get($data, "remember_me") : false);
        $loginData = Arr::only($data, ["country_code", "phone", "password"]);
        //$loginData['is_waseet_user'] = 0;
        if ($token = JWTAuth::attempt($loginData, $rememberMe)) {


            if (Auth::user()->phone_verified_at == null) {
                return new GenericPayload( __('error.accountNotActivated'), 425
                );
            }

            if (!Auth::user()->is_active) {
                return new GenericPayload( __('error.inActiveUser'), 422
                );
            }

            $this->incrementLoginNum();

            if(isset($data['device_token'])) {
                Auth::user()->tokens()->firstOrCreate([
                    'device_token' => $data['device_token']
                ]);
            }

             if(isset($data['device_id'])) {
                 $cart = Cart::whereDeviceId($data['device_id'])->get();
                // if(count($cart) > 0){
                     foreach ($cart as $item) {
                         $item->update([
                             'user_id' => Auth()->id(),
                             'device_id' => null
                         ]);
                     }
               //  }
             }

            //return new GenericPayload($this->respondWithToken($token));
            return new GenericPayload(
                array_merge(
                    $this->respondWithToken($token),
                    [
                        'message' => __('success.accountVerified'),
                        "user" => new UserResource(Auth::user()),
                    ]
                ),
                Response::HTTP_RESET_CONTENT
            );
        }

        return new GenericPayload( __('error.wrongLoginData'), 422);
    }

    private function incrementLoginNum()
    {
        Auth::user()->increment('login_numbers', 1, ['last_login_at' => Carbon::now()]);
    }

    /**
     * Get the token array structure.
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
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
