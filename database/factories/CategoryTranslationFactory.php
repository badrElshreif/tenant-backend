<?php

namespace Database\Factories;

use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Category\Domain\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryTranslationFactory extends Factory
{
    protected $model = CategoryTranslation::class;

    public function definition()
    {
        return [
            'category_id' => Category::factory(),
            'locale' => $this->faker->randomElement(['en', 'ar']),
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->sentence(20),
        ];
    }
}
