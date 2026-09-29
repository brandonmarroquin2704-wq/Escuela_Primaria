<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            ModuloSeeder::class,
            PermisoSeeder::class,
            RolPermisoSeeder::class,
        ]);
    }
}