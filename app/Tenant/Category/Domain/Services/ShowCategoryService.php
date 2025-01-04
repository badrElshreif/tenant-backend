<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Category\Domain\Resources\CategoryResource;
use Symfony\Component\HttpFoundation\Response;

class ShowCategoryService extends Service
{
    public function handle($data = [])
    {
        $category = Category::findOrFail($data['category_id']);

        return [
            'status' => true,
            'data' => new CategoryResource($category),
            'message' => __('success.foundSuccessfully'),
        ];
    }
}
