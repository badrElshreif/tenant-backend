<?php

namespace App\Tenant\AppContent\Domain\Services\API;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\Faq;
use App\Infrastructure\Exceptions\ModelNotFoundException;

class CreateFaqService extends Service
{
    public function handle($data = [])
    {
        try {
            $faq = Faq::create($data);
        } catch (Exception $ex) {
            return new GenericPayload(
                ['message' => __('error.someThingWrong')], 422
            );
        }
        return new GenericPayload($faq);

    }
}
