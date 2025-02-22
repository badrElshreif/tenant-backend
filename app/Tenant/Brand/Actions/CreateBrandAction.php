<?php

namespace App\Tenant\Brand\Actions;

use App\Infrastructure\Responders\GenericResponder;
use App\Tenant\Brand\Domain\Requests\BrandRequest;
use App\Tenant\Brand\Domain\Services\CreateBrandService;
use Illuminate\Http\JsonResponse;

class CreateBrandAction
{
    /**
     * @var GenericResponder
     */
    private GenericResponder $responder;

    /**
     * @var CreateBrandService
     */
    private CreateBrandService $services;

    /**
     * Create a new CreateBrandAction instance.
     *
     * @param GenericResponder $responder
     * @param CreateBrandService $services
     */
    public function __construct(
        GenericResponder $responder,
        CreateBrandService $services
    ) {
        $this->responder = $responder;
        $this->services = $services;
    }

    /**
     * Handle the incoming request.
     *
     * @param BrandRequest $request
     * @return JsonResponse
     */
    public function __invoke(BrandRequest $request): JsonResponse
    {
        return $this->responder
            ->withResponse($this->services->handle($request->validated()))
            ->respond();
    }
}
