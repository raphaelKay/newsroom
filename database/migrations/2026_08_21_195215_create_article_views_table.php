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
        Schema::create('article_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles');
            $table->ipAddress('ip_address');
            $table->text('user_agent');
            $table->timestamp('viewed_at');

            $table->index(['article_id', 'viewed_at',]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_views');
    }
};
