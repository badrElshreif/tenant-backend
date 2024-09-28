<?php

namespace App\Tenant\AppContent\Actions\Page;
use App\Tenant\AppContent\Domain\Services\Page\GetPageService;
use App\Tenant\AppContent\Responders\PageResponder;

class GetPageAction
{

    private $service, $responder;

    public function __construct(PageResponder $responder, GetPageService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke($slug)
    {
        return $this->responder->withResponse(
           $this->service->handle(["slug" => $slug])
        )->respond();
    }
}
