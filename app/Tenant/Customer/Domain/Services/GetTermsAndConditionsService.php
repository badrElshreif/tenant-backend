<?php

namespace App\User\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Infrastructure\Models\Page;
use Symfony\Component\HttpFoundation\Response;
class GetTermsAndConditionsService extends Service
{
    public function handle()
    {
        $terms = Page::whereSlug('terms-conditions')->select('title', 'body', 'slug')->first();
        return new GenericPayload($terms, Response::HTTP_CREATED);
    }
}
