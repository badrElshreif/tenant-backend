<?php

namespace App\User\Domain\Services\User;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use Symfony\Component\HttpFoundation\Response;

class GetProfileService extends Service
{
    public function handle($data = [])
    {
        $user = auth()->user();
        return new GenericPayload($user, Response::HTTP_CREATED);
    }
}
