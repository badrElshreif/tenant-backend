<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use Symfony\Component\HttpFoundation\Response;

class DeleteCategoryService extends Service
{
    public function handle($data = [])
    {
        $category = Category::findOrFail($data['category_id']);
        try {
            if ($category->parent_id != null || $category->type == 'centers') {
//                if (count($category->products()->get()) > 0)
//                return [
//                    'status' => false,
//                    'message' => __('error.cannotDelete'),
//                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
//                ];
            } else {
                if (count($category->childs()->get()) > 0)
                    return [
                        'status' => false,
                        'message' => __('error.cannotDeleteHasSubCategories'),
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    ];
            }
            $category->delete();
            return [
                'status' => true,
                'message' => __('success.deletedSuccessfuly'),
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
