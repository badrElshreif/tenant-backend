<?php

namespace App\Tenant\AppContent\Domain\Services\Page;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\Page;
use Symfony\Component\HttpFoundation\Response;

class ListPagesService extends Service
{
    public function handle($data = [])
    {
        $target = 'admin';
        $pages = Page::whereTarget($target)->whereIsActive(1)->get();

        return new GenericPayload($pages, Response::HTTP_OK);
    }
}
