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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('comment_uidentity', 10)->unique();
            $table->text('body');
            $table->foreignId('parent_id')->nullable()->constrained('comments');
            $table->foreignId('article_id')->constrained('articles');
            $table->foreignId('author_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // $table->unique(['article_id', 'author_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
