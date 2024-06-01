<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Category\Domain\Filters\CategoryFilter;
use Symfony\Component\HttpFoundation\Response;

class ListAllSubCategoriesService extends Service
{
    protected $category, $filter;

    public function __construct(Category $category, CategoryFilter $filter)
    {
        $this->category = $category;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        if(isset($data['category_id'])){
            $categories = $this->category->where('parent_id', $data['category_id'])->filter($this->filter);
        }else{
            $categories = $this->category->whereNotNull('parent_id')->filter($this->filter);
        }

        $categories = $categories->active(1)->orderBy('order', 'ASC')->get();
        return new GenericPayload($categories, Response::HTTP_OK);
    }
}
