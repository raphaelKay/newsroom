<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // Query users with authorized roles or moderate permission
        $eligible_authors = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [
                'editor',
                'author',
                'super-administrator',
                'super-admin',
            ]);
        })->orWhereHas('permissions', function ($query) {
            $query->where('name', 'moderate articles');
        })->get();

        $categories = Category::all();
        $tags = Tag::all();

        // Safety checks
        if ($eligible_authors->isEmpty()) {
            throw new RuntimeException(
                'ArticleSeeder failed: No authorized authors found. Seed users and roles first.'
            );
        }

        if ($categories->isEmpty()) {
            throw new RuntimeException(
                'ArticleSeeder failed: No categories found in the database. Seed categories first.'
            );
        }

        if ($tags->isEmpty()) {
            throw new RuntimeException(
                'ArticleSeeder failed: No tags found in the database. Seed tags first.'
            );
        }

        // Helper closure to create articles and attach 2-5 random tags
        $createArticlesWithTags = function (int $count, string $state = null) use ($eligible_authors, $categories, $tags) {
            $factory = Article::factory($count);
            
            if ($state && method_exists($factory, $state)) {
                $factory = $factory->{$state}();
            }

            $articles = $factory
                ->recycle($eligible_authors)
                ->recycle($categories)
                ->create();

            // Attach 2 to 5 random existing tags to each created article
            $articles->each(function (Article $article) use ($tags) {
                $randomTagIds = $tags->random(rand(2, min(5, $tags->count())))->pluck('id');
                $article->tags()->sync($randomTagIds);
            });
        };

        // 1. Create Breaking News items (with 2-5 tags)
        $createArticlesWithTags(2, 'breaking');

        // 2. Create Published Articles (with 2-5 tags)
        $createArticlesWithTags(25, 'published');

        // 3. Create Draft Articles (with 2-5 tags)
        $createArticlesWithTags(5, 'draft');
    }
}