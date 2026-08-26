<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Polymorphic pivot / container table
        Schema::create('article_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('article_gallery_uidentity', 10)->unique();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->morphs('media');
            $table->string('caption')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Specific table for Images
        Schema::create('article_images', function (Blueprint $table) {
            $table->id();
            $table->string('article_image_uidentity', 10)->unique();
            $table->string('file_path');
            $table->string('mime_type');
            $table->string('alt_text')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Specific table for Videos
        Schema::create('article_videos', function (Blueprint $table) {
            $table->id();
            $table->string('article_video_uidentity', 10)->unique();
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('duration_seconds');
            $table->string('thumbnail_path')->nullable();
            $table->string('aspect_ratio')->default('16:9');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_galleries');
        Schema::dropIfExists('article_images');
        Schema::dropIfExists('article_videos');
    }
};