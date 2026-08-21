<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('article_uidentity', 10)->unique();
            $table->string('title', 150)->unique();
            $table->string('slug', 255)->unique();
            $table->text('body');
            $table->text('excerpt');
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'published'])->default('draft');
            $table->boolean('is_breaking')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('category_id')->constrained('categories');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index('slug');
            $table->index('is_breaking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
