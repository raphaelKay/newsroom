<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use App\Traits\HasUIdentity;

#[Fillable(['article_gallery_uidentity', 'article_id', 'media_id', 'media_type', 'caption', 'display_order'])]
#[Hidden(['id', 'article_gallery_uidentity', 'created_at', 'updated_at', 'deleted_at'])]
class ArticleGallery extends Model
{
    use SoftDeletes, HasUIdentity;

    protected $uidentity_column = 'article_gallery_uidentity';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function media(): MorphTo
    {
        return $this->morphTo();
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}