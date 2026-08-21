<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

#[Fillable(['article_id', 'user_agent', 'ip_address', 'viewed_at'])]
#[Hidden(['id'])]
class ArticleView extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleViewFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    public function article(): BelongsTo {
        return $this->belongsTo(Article::class);
    }
}
