<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
use App\Traits\HasSlug;
use App\Traits\HasUIdentity;

#[Fillable(['category_uidentity', 'name', 'slug', 'parent_id'])]
#[Hidden(['id', 'category_uidentity'])]
class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, HasRecursiveRelationships, HasUIdentity, HasSlug, SoftDeletes;

    protected $uidentity_column = 'category_uidentity';

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
}
