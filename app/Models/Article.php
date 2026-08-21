<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use App\Traits\HasSlug;
use App\Traits\HasUIdentity;

#[Fillable(['article_uidentity', 'title', 'slug', 'body', 'excerpt', 'featured_image', 'status', 'is_breaking', 'published_at', 'author_id', 'category_id'])]
#[Hidden(['id', 'article_uidentity', 'created_at', 'updated_at', 'deleted_at'])]
class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory, HasSlug, HasUIdentity, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_breaking' => 'boolean',
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany {
        return $this->belongsToMany(Tag::class);
    }

    public function views(): HasMany {
        return $this->hasMany(ArticleView::class);
    }

    public function scopePublished(Builder $query): Builder {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }

    public function getReadingTimeAttribute(): int {
        return (int) ceil(str_word_count( strip_tags( $this->body)) / 200);
    }
}
