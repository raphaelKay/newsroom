<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use App\Traits\HasSlug;
use App\Traits\HasUIdentity;

#[Fillable(['category_uidentity', 'name', 'slug', 'parent_id'])]
#[Hidden(['id', 'tag_uidentity', 'created_at', 'updated_at', 'deleted_at'])]
class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory, HasSlug, HasUIdentity, SoftDeletes;

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

    public function articles(): BelongsToMany {
        return $this->belongsToMany(Article::class);
    }
}
