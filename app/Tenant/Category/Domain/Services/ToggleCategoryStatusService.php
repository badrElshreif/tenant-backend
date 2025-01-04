<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Category\Domain\Resources\CategoryResource;
use Symfony\Component\HttpFoundation\Response;

class ToggleCategoryStatusService extends Service
{
    public function handle($data = [])
    {
        $category = Category::findOrFail($data['category_id']);
        try {
            if ($category->is_active) {
                if ($category->parent_id != null || $category->type == 'centers') {
                    if (count($category->products()->active(1)->get()) > 0)
                        return [
                            'status' => false,
                            'message' => __('error.cannotDeactivate'),
                            'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        ];
                } else {
//                    if (count($category->childs()->active(1)->get()) > 0)
//                        return [
//                            'status' => false,
//                            'message' => __('error.cannotDeactivate'),
//                            'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
//                        ];
                }
            }
            $category->update([
                'is_active' => !$category->is_active
            ]);

            return [
                'data' => new CategoryResource($category),
                'status' => true,
                'message' => __('success.updatedSuccessfully'),
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
