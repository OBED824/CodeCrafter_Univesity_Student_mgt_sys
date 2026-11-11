<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage students',
            'manage courses',
            'manage registrations',
            'manage users',
            'view system info',
            'view own courses',
            'view course students',
            'register courses',
            'view own registrations',
            'view assignments',
            'manage assignments',
            'view own grades',
            'view course grades',
            'manage grades',
            'view course notes',
            'manage course notes',
            'view own attendance',
            'record attendance',
            'manage attendance',
            'view reports',
            'manage student lifecycle',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $administrator = Role::firstOrCreate(['name' => 'administrator']);
        $administrator->syncPermissions(Permission::all());

        $lecturer = Role::firstOrCreate(['name' => 'lecturer']);
        $lecturer->syncPermissions([
            'view own courses',
            'view course students',
            'view assignments',
            'manage assignments',
            'view course grades',
            'manage grades',
            'view course notes',
            'manage course notes',
        ]);

        $student = Role::firstOrCreate(['name' => 'student']);
        $student->syncPermissions([
            'register courses',
            'view own registrations',
            'view assignments',
            'view own grades',
            'view course notes',
            'view own attendance',
            'record attendance',
        ]);
    }
}
