<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RolePermissionSeeder::class,
            SettingsSeeder::class,
            DivisionSeeder::class,
            DistrictSeeder::class,
            AreaSeeder::class,
            UnionSeeder::class,
            ContentSeeder::class,
        ]);
    }
}
