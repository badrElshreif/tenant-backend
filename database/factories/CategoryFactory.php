<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Tenant\Category\Domain\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
         //   'slug' => $this->faker->unique()->slug,
            'parent_id' => null,
        ];
    }

    // You can define a state for subcategories (with a parent)
    public function withParent(Category $parent)
    {
        return $this->state(function () use ($parent) {
            return ['parent_id' => $parent->id];
        });
    }
}
