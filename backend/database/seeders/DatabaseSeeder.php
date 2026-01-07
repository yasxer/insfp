<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SpecialtySeeder::class,
            ModuleSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            AdministrationSeeder::class,
        ]);
    }
}
