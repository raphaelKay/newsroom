<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'body' => fake()->paragraph(rand(1, 3)),
            'parent_id' => null,
            'article_id' => Article::inRandomOrder()->first()?->id,
            'author_id' => User::inRandomOrder()->first()?->id,
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Mark the comment as a child reply to another comment.
     */
    public function replyOf(Comment $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'article_id' => $parent->article_id,
            'created_at' => fake()->dateTimeBetween($parent->created_at, 'now'),
        ]);
    }
}