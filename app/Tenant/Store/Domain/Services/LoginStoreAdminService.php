<?php

namespace App\Tenant\Store\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Payloads\UnauthorizedPayload;
use App\Infrastructure\Domain\Services\Service;
use Illuminate\Support\Arr;

class LoginStoreAdminService extends Service
{
    public function handle($data = [])
    {
        $rememberMe = boolval(Arr::has($data, "remember_me") ? Arr::get($data, "remember_me") : false);
        $loginData = Arr::only($data, ["email", "password"]);
        if (auth("store")->attempt($loginData, $rememberMe) && ($isActivated = auth("store")->user()->is_active)) {
            return new GenericPayload(auth("store")->user());
        }

        if (isset($isActivated) && !$isActivated) {
            auth("store")->logout();
            return new UnauthorizedPayload([
                'userIsNoActive' => 'Customer is not activated yet.',
            ]);
        }
        return new UnauthorizedPayload;
    }
}
