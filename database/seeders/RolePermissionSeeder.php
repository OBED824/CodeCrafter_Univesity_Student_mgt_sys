<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Creates the three system roles and their permissions.
 *
 * firstOrCreate is used everywhere so running the seeder again
 * never creates duplicate roles or permissions.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset Spatie's cached permissions so the new ones are seen immediately.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Each permission is one specific action a role is allowed to do.
        $permissions = [
            'manage students',       // administrator only
            'manage courses',        // administrator only
            'manage registrations',  // administrator only
            'manage users',          // administrator only
            'view system info',      // administrator only
            'view own courses',      // lecturer
            'view course students',  // lecturer
            'register courses',      // student
            'view own registrations', // student
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Administrator: everything.
        $administrator = Role::firstOrCreate(['name' => 'administrator']);
        $administrator->syncPermissions(Permission::all());

        // Lecturer: read-only access to their own courses and their students.
        $lecturer = Role::firstOrCreate(['name' => 'lecturer']);
        $lecturer->syncPermissions([
            'view own courses',
            'view course students',
        ]);

        // Student: browse courses and manage only their own registrations.
        $student = Role::firstOrCreate(['name' => 'student']);
        $student->syncPermissions([
            'register courses',
            'view own registrations',
        ]);
    }
}
