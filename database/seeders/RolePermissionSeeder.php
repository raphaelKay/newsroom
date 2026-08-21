<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = [
            'super-administrator',
            'editor',
            'author',
            'reader',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Permissions
        $permissions = [
            'moderate article',
            'moderate editor',
            'moderate author',
            'moderate comment',
            'can comment',
            'react',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles

        // Admin: full access
        Role::findByName('super-administrator')->syncPermissions(Permission::all());

        // Stockkeeper: catalog + inventory + fulfillment
        Role::findByName('editor')->syncPermissions([
            'moderate article',
            'moderate author',
            'moderate comment',
            'can comment',
            'react',
        ]);

        // Customer: self-service only
        Role::findByName('author')->syncPermissions([
            'moderate article',
            'can comment',
            'react',
        ]);

        // Support: customer service
        Role::findByName('reader')->syncPermissions([
            'can comment',
            'react',
        ]);
    }
}