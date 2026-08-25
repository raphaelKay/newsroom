<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'article_uidentity' => strtoupper(Str::random(10)),
            'title' => Str::limit($title, 150, ''),
            'slug' => Str::slug($title),
            'body' => collect(fake()->paragraphs(6))
                ->map(fn ($p) => "<p>{$p}</p>")
                ->implode(''),
            'excerpt' => fake()->paragraph(2),
            'featured_image' => 'articles/default-' . fake()->numberBetween(1, 5) . '.jpg',
            'status' => fake()->randomElement(['draft', 'scheduled', 'published']),
            'is_breaking' => fake()->boolean(10), // 10% chance
            'published_at' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
            'author_id' => User::inRandomOrder()->first()?->id,
            'category_id' => Category::inRandomOrder()->first()?->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State for published articles.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * State for draft articles.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
            'is_breaking' => false,
        ]);
    }

    /**
     * State for breaking news articles.
     */
    public function breaking(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'is_breaking' => true,
            'published_at' => now()->subMinutes(fake()->numberBetween(5, 120)),
        ]);
    }
}