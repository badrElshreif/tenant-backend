<?php

namespace App\Tenant\AppContent\Domain\Services\ContactUs;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\ContactType;
use App\Tenant\AppContent\Domain\Filters\ContactUsFilter;
use Symfony\Component\HttpFoundation\Response;
use App\Tenant\AppContent\Domain\Resources\ContactTypeResource;

class ListContactTypesService extends Service
{
    public function handle($data = [])
    {
        $types = ContactType::whereIsActive(1)->get();

        return new GenericPayload(ContactTypeResource::collection($types) , Response::HTTP_RESET_CONTENT);
    }
}
