<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use Symfony\Component\HttpFoundation\Response;

class ShowCategoryService extends Service
{
    public function handle($data = [])
    {
        $category = Category::findOrFail($data['category_id']);
        return new GenericPayload($category, Response::HTTP_OK,'resource');
    }
}
