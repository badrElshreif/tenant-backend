<?php

namespace App\Tenant\AppContent\Actions\Page;
use App\Tenant\AppContent\Domain\Requests\PageRequest;
use App\Tenant\AppContent\Domain\Services\Page\UpdatePageService;
use App\Tenant\AppContent\Responders\PageResponder;

class UpdatePageAction
{
    public function __construct(PageResponder $responder, UpdatePageService $services)
    {
        $this->responder = $responder;
        $this->services = $services;
    }

    public function __invoke(PageRequest $request, $slug)
    {
        return $this->responder->withResponse(
            $this->services->handle(array_merge($request->validated(), ["slug" => $slug]))
        )->respond();
    }
}
