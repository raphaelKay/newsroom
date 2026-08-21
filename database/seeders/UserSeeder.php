<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'firstname' => 'Raphael',
            'lastname' => 'Kwami',
            'email' => 'raphaelkay@live.com',
            'gender' => 'male',
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ])->each(function($user) {
            $user->assignRole([
                'super-administrator',            
            ]);
        });

        User::factory(2)->create()->each(function($user) {
            $user->assignRole([
                'editor',
            ]);
        });

        User::factory(1)->create()->each(function($user) {
            $user->assignRole([
                'author',
            ]);
        });

        User::factory(5)->create()->each(function($user) {
            $user->assignRole([
                'reader',
            ]);
        });
    }
}
