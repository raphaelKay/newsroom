<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use App\Traits\HasUIdentity;

#[Fillable(['article_video_uidentity', 'file_path', 'mime_type', 'duration_seconds', 'thumbnail_path', 'aspect_ratio'])]
#[Hidden(['id', 'article_video_uidentity', 'created_at', 'updated_at', 'deleted_at'])]
class ArticleVideo extends Model
{
    use SoftDeletes, HasUIdentity;

    protected $uidentity_column = 'article_video_uidentity';

    public function galleryItem(): MorphOne
    {
        return $this->morphOne(ArticleGallery::class, 'media');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
