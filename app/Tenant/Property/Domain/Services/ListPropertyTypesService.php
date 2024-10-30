<?php

namespace App\Property\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Property\Domain\Models\PropertyType;
use Symfony\Component\HttpFoundation\Response;

class ListPropertyTypesService extends Service
{
    protected $category;

    public function __construct(PropertyType $type)
    {
        $this->type = $type;
    }

    public function handle($data = []) 
    {
        $types = $this->type->whereIsActive(1)->get();
        return new GenericPayload($types, Response::HTTP_OK);
    }
}