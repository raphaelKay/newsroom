<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait HasUIdentity
{
    protected static function bootHasUIdentity(): void
    {
        static::creating(function ($model) {

            $column = $model->getUIdentityColumn();

            if (!$column || !empty($model->{$column})) {
                return;
            }

            $model->{$column} = $model->generateUIdentity();
        });
    }

    protected function getUIdentityColumn(): ?string
    {
        return property_exists($this, 'uidentity_column') ? $this->uidentity_column : 'uidentity';
    }

    protected function generateUIdentity(int $length = 10): string
    {
        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max      = strlen($alphabet) - 1;

        $query = static::query();

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            $query->withTrashed();
        }

        do {
            $value = '';
            for ($i = 0; $i < $length; $i++) {
                $value .= $alphabet[random_int(0, $max)];
            }
        } while ($query->where($this->getUIdentityColumn(), $value)->exists());

        return $value;
    }
}
