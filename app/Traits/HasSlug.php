<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {

            $slugColumn = $model->getSlugColumn();
            $slugSource = $model->getSlugSource();

            if (!$slugColumn || !$slugSource || !empty($model->{$slugColumn})) {
                return;
            }

            $base = Str::slug(strtolower($model->{$slugSource}));

            $model->{$slugColumn} = $model->shouldUseSlugSuffix() ? $model->generateSlugWithSuffix($base) : $model->generateSlugWithoutSuffix($base);
        });
    }

    protected function shouldUseSlugSuffix(): bool {
        return property_exists($this, 'slug_with_suffix') ? (bool) $this->slug_with_suffix : false;
    }

    protected function getSlugColumn(): ?string {
        return property_exists($this, 'slug_column') ? $this->slug_column : 'slug';
    }

    protected function getSlugSource(): ?string {
        return property_exists($this, 'slug_source') ? $this->slug_source : 'name';
    }

    protected function generateSlugWithoutSuffix(string $base): string {
        return $this->ensureSlugIsUnique(fn () => $base);
    }

    protected function generateSlugWithSuffix(string $base): string {
        return $this->ensureSlugIsUnique(function () use ($base) {
            return $base . '-' . Str::lower(Str::random(6));
        });
    }

    protected function ensureSlugIsUnique(callable $generator): string {
        $query = static::query();

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            $query->withTrashed();
        }

        do {
            $slug = $generator();
        } while (
            $query->where($this->getSlugColumn(), $slug)->exists()
        );

        return $slug;
    }
}
