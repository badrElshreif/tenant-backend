<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Category\Domain\Resources\CategoryResource;
use Symfony\Component\HttpFoundation\Response;

class CreateCategoryService extends Service
{
    public function handle($data = [])
    {
        try {
            $data['is_active'] = isset($data['is_active']) ? $data['is_active'] : 1;
            $data['type'] = isset($data['type']) ? $data['type'] : 'stores';
            $category = Category::create($data);

            return [
                'data' => new CategoryResource($category),
                'status' => true,
                'message' => __('success.crreatedSuccessfully'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }
    }
}
