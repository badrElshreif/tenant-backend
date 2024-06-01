<?php

namespace App\Tenant\Order\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Order\Domain\Models\Status;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ListOrderStatusesService extends Service
{
    public function handle($data = [])
    {
        $type = request()->get('type', 'order');
        $statuses = Status::active()->whereType($type)->get();
        return new GenericPayload($statuses, Response::HTTP_OK);
    }
}
