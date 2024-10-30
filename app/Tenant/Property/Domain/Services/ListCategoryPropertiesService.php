<?php

namespace App\Property\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Category\Domain\Models\Category;
use Symfony\Component\HttpFoundation\Response;

class ListCategoryPropertiesService extends Service
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function handle($data = []) 
    {
        try {
            $category = Category::findOrFail($data['category_id']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                ['message' => __('error.someThingWrong')], 422
            );
        }
        return new GenericPayload($category->properties, Response::HTTP_OK);
    }
}