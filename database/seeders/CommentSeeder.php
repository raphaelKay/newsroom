<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $articles = Article::where('status', 'published')->get();

        if ($users->isEmpty() || $articles->isEmpty()) {
            throw new RuntimeException(
                'CommentSeeder failed: Users and published articles must be seeded prior to comments.'
            );
        }

        // Pick 60% of published articles to receive comments (leaving ~40% without comments)
        $selectedArticles = $articles->random((int) ($articles->count() * 0.6));

        $selectedArticles->each(function (Article $article) use ($users) {
            // 1. Create 2 to 6 top-level comments for this article
            $topLevelComments = Comment::factory(rand(2, 6))
                ->recycle($users)
                ->create([
                    'article_id' => $article->id,
                    'parent_id' => null,
                ]);

            // 2. Select a subset of top-level comments (e.g., 40%) to receive child replies
            $commentsToReceiveReplies = $topLevelComments->random((int) max(1, $topLevelComments->count() * 0.4));

            $commentsToReceiveReplies->each(function (Comment $parentComment) use ($users) {
                // Generate 1 to 3 direct replies per selected top-level comment
                Comment::factory(rand(1, 3))
                    ->recycle($users)
                    ->replyOf($parentComment)
                    ->create();
            });
        });
    }
}