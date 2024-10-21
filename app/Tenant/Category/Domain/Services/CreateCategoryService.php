<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use Symfony\Component\HttpFoundation\Response;

class CreateCategoryService extends Service
{
    public function handle($data = [])
    {
        $data['is_active'] = isset($data['is_active']) ? $data['is_active'] : 1;
        $data['type'] = isset($data['type']) ? $data['type'] : 'stores';
        $category = Category::create($data);
        return new GenericPayload($category, Response::HTTP_CREATED);

    }
}
