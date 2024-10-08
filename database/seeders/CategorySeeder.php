<?php

namespace Database\Seeders;

use App\Tenant\Category\Domain\Models\Category;
use App\Tenant\Category\Domain\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{


    public function run()
    {

        $locales = ['en', 'ar'];

        // Create 5 top-level categories
        Category::factory(30)->create()->each(function ($category) use ($locales) {
            // Create translations for each top-level category
            foreach ($locales as $locale) {
                CategoryTranslation::factory()->create([
                    'category_id' => $category->id,
                    'locale' => $locale,
                  //  'name' => ucfirst($category->slug) . " in " . $locale,
                ]);
            }
//
//            // Create 3 subcategories for each top-level category
//            Category::factory(3)->withParent($category)->create()->each(function ($subCategory) use ($locales) {
//                foreach ($locales as $locale) {
//                    CategoryTranslation::factory()->create([
//                        'category_id' => $subCategory->id,
//                        'locale' => $locale,
//                    ]);
//                }
//            });
        });
    }
}
