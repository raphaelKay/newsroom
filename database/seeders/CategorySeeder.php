<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'World' => [
                'Africa' => [],
                'Americas' => [],
                'Asia & Pacific' => [],
                'Europe' => [],
                'Middle East' => [],
                'Global Affairs' => [],
            ],

            'Politics' => [
                'Government & Policy' => [],
                'Elections' => [],
                'Courts & Justice' => [],
                'Diplomacy' => [],
            ],

            'Business & Economy' => [
                'Markets & Finance' => [],
                'Economy & Inflation' => [],
                'Real Estate' => [],
                'Startups & Innovation' => [],
                'Energy & Commodities' => [],
            ],

            'Technology' => [
                'Artificial Intelligence' => [],
                'Cybersecurity' => [],
                'Mobile & Gadgets' => [],
                'Software & Apps' => [],
                'Big Tech' => [],
            ],

            'Science & Climate' => [
                'Climate & Environment' => [],
                'Space & Astronomy' => [],
                'Health & Medicine' => [],
                'Biology & Innovation' => [],
            ],

            'Entertainment & Culture' => [
                'Movies & TV' => [],
                'Music' => [],
                'Books & Literature' => [],
                'Celebrity & Pop Culture' => [],
                'Gaming' => [],
            ],

            'Sports' => [
                'Football / Soccer' => [],
                'Basketball' => [],
                'Tennis' => [],
                'Motorsport' => [],
                'Athletics' => [],
                'Combat Sports' => [],
            ],

            'Lifestyle' => [
                'Health & Wellness' => [],
                'Food & Dining' => [],
                'Travel & Exploration' => [],
                'Fashion & Style' => [],
                'Personal Finance' => [],
            ],

            'Opinion & Editorial' => [
                'Editorials' => [],
                'Columnists' => [],
                'Op-Eds' => [],
                'Letters to Editor' => [],
            ],

            'Investigations & Reports' => [
                'Special Reports' => [],
                'Fact Checks' => [],
                'Data Journalism' => [],
                'Documentaries' => [],
            ],
        ];

        $this->createCategories($categories);
    }

    protected function createCategories(array $categories, ?int $parent_id = null): void
    {
        foreach ($categories as $category_name => $subcategories) {
            $category = Category::create([
                'name' => $category_name,
                'parent_id' => $parent_id,
                'created_by' => 1,
            ]);

            if (!empty($subcategories)) {
                $this->createCategories($subcategories, $category->id);
            }
        }
    }
}