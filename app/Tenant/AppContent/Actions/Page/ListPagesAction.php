<?php

namespace App\Tenant\AppContent\Actions\Page;

use App\Tenant\AppContent\Domain\Services\Page\ListPagesService;
use App\Tenant\AppContent\Responders\PageResponder;

class ListPagesAction
{
    public function __construct(PageResponder $responder, ListPagesService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke()
    {
        return $this->responder->withResponse(
            $this->services->handle()
        )->respond();
    }
}
