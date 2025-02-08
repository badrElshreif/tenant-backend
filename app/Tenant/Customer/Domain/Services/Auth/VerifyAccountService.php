<?php

namespace App\User\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\User\Domain\Models\Customer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use App\Infrastructure\Exceptions\UserNotFoundException;
use App\User\Domain\Resources\UserResource;
use JWTAuth;
use App\Order\Domain\Models\Cart;
use App\Order\Domain\Models\OrderGift;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccountService extends Service
{
    private $user;

    public function __construct(Customer $user)
    {
        $this->user = $user;
    }

    public function handle($data = null)
    {
        try {
            if(str_starts_with($data['phone'], '0')){
                $data['phone'] = substr($data['phone'],1,20);
            }

            $user = $this->user->whereCountryCode($data['country_code'])->wherePhone($data["phone"])->firstOrFail();
            if($user->phone_verified_at != NULL)
                return new GenericPayload(
                     __('error.alreadyVerifiedAccount'), 422
                );
            if ($user->verification_code == $data['code']) {
                $data["phone_verified_at"] = now();
                $data['is_active'] = 1;
                $data["verification_code"] = null;
                $user->update(Arr::only($data, ["phone_verified_at", "is_active", "verification_code"]));
                $token = JWTAuth::fromUser($user);
                if(isset($data['device_token'])) {
                    $user->tokens()->firstOrCreate([
                        'device_token' => $data['device_token']
                    ]);
                }

                // if(isset($data['device_id'])) {
                //     $cart = Cart::whereDeviceId($data['device_id'])->get();
                //     if(count($cart) > 0){
                //         foreach ($cart as $item) {
                //             $item->update([
                //                 'user_id' => $user->id,
                //                 'device_id' => null
                //             ]);
                //         }
                //     }
                // }

                // $gifts = OrderGift::wherePhone($data["phone"])->get();
                // if(count($gifts) > 0){
                //     foreach ($gifts as $gift) {
                //         $gift->update([
                //             'user_id' => $user->id
                //         ]);
                //     }
                // }

                return new GenericPayload(
                    array_merge(
                        [
                            'message' => __('success.accountVerified'),
                            "user" => new UserResource($user),
                        ],
                        $this->respondWithToken($token),
                    ),
                    Response::HTTP_RESET_CONTENT
                );
            }
            return new GenericPayload(
                __('error.invalidCode'), 422
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new UserNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
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
