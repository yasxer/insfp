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
            AdministrationSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            ModuleSeeder::class,
            TeacherModuleSeeder::class,
            ScheduleSeeder::class,
            HolidaySeeder::class,
        ]);
    }
}
