<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasUIdentity;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_uidentity', 'firstname', 'lastname', 'gender', 'email', 'password'])]
#[Hidden(['id', 'password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token', 'user_uidentity', 'email_verified_at', 'created_at', 'updated_at', 'deleted_at'])]
class User extends Authenticatable implements PasskeyUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable, HasRoles, HasUIdentity, SoftDeletes;

    protected $uidentity_column = 'user_uidentity';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        // return Str::of($this->name)->explode(' ')->take(2)->map(fn ($word) => Str::substr($word, 0, 1))->implode('');
        return Str::substr($this->firstname, 0, 1) . Str::substr($this->lastname, 0, 1);
    }

    /**
     * Get the user's fullname
     */

    public function getNameAttribute (): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
