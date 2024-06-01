<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Category\Domain\Filters\CategoryFilter;
use Symfony\Component\HttpFoundation\Response;

class ListAllCategoriesService extends Service
{
    protected $category, $filter;

    public function __construct(Category $category, CategoryFilter $filter)
    {
        $this->category = $category;
        $this->filter = $filter;
    }

    public function handle($data = [])
    {
        $categories = $this->category->whereNull('parent_id')->parent()->active(1)
            ->whereHas('childs.products', function ($q) {
                $q->where('is_active', 1);
            })
            ->orderBy('order', 'ASC')->get();
        return new GenericPayload($categories, Response::HTTP_OK);
    }

}
