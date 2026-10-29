<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Runs all seeders in the correct order:
 * roles/permissions first, then sample data that uses those roles.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            DataSeeder::class,
        ]);
    }
}
