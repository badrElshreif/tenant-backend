<?php

namespace App\Tenant\AppContent\Domain\Services\ContactUs;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\ContactUs;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class CreateContactUsService extends Service
{
    public function handle($data = [])
    {
        try {
            $contact_us = ContactUs::create($data);
            return new GenericPayload($contact_us, Response::HTTP_CREATED);
        } catch (Exception $ex) {
            return new GenericPayload(
                ['message' => __('error.someThingWrong')], 422
            );
        }


    }
}
