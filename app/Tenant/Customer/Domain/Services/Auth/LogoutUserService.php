<?php

namespace App\User\Domain\Services\Auth;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use Symfony\Component\HttpFoundation\Response;

class LogoutUserService extends Service
{
    public function handle($data = [])
    {
        try {
	        //auth()->user()->tokens()->delete();
	        optional(auth()->user()->tokens->where('device_token', $data['device_token'])->first())->delete();
	        auth("api")->logout();
	        return new GenericPayload(['message' =>'success'], Response::HTTP_NO_CONTENT);
	    } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }

    }
}
