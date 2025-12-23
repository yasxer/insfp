<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // DD Specialty (specialty_id: 1, semester: 1)
        Schedule::create(['module_id' => 1, 'teacher_id' => 1, 'specialty_id' => 1, 'day' => 'monday', 'start_time' => '08:00', 'classroom' => 'A101', 'semester' => 1, 'academic_year' => '2024-2025']);
        Schedule::create(['module_id' => 2, 'teacher_id' => 1, 'specialty_id' => 1, 'day' => 'monday', 'start_time' => '10:00', 'classroom' => 'A102', 'semester' => 1, 'academic_year' => '2024-2025']);
        Schedule::create(['module_id' => 3, 'teacher_id' => 1, 'specialty_id' => 1, 'day' => 'tuesday', 'start_time' => '08:00', 'classroom' => 'A101', 'semester' => 1, 'academic_year' => '2024-2025']);
        Schedule::create(['module_id' => 4, 'teacher_id' => 5, 'specialty_id' => 1, 'day' => 'wednesday', 'start_time' => '08:00', 'classroom' => 'A103', 'semester' => 1, 'academic_year' => '2024-2025']);

        // GE Specialty (specialty_id: 2, semester: 1)
        Schedule::create(['module_id' => 13, 'teacher_id' => 4, 'specialty_id' => 2, 'day' => 'monday', 'start_time' => '08:00', 'classroom' => 'B101', 'semester' => 1, 'academic_year' => '2024-2025']);
        Schedule::create(['module_id' => 14, 'teacher_id' => 2, 'specialty_id' => 2, 'day' => 'tuesday', 'start_time' => '10:00', 'classroom' => 'B102', 'semester' => 1, 'academic_year' => '2024-2025']);
        Schedule::create(['module_id' => 15, 'teacher_id' => 2, 'specialty_id' => 2, 'day' => 'wednesday', 'start_time' => '08:00', 'classroom' => 'B101', 'semester' => 1, 'academic_year' => '2024-2025']);
        Schedule::create(['module_id' => 16, 'teacher_id' => 5, 'specialty_id' => 2, 'day' => 'thursday', 'start_time' => '08:00', 'classroom' => 'B103', 'semester' => 1, 'academic_year' => '2024-2025']);

        // CF Specialty (specialty_id: 3, semester: 1)
        Schedule::create(['module_id' => 23, 'teacher_id' => 4, 'specialty_id' => 3, 'day' => 'monday', 'start_time' => '10:00', 'classroom' => 'C101', 'semester' => 1, 'academic_year' => '2024-2025']);
        Schedule::create(['module_id' => 24, 'teacher_id' => 4, 'specialty_id' => 3, 'day' => 'tuesday', 'start_time' => '08:00', 'classroom' => 'C101', 'semester' => 1, 'academic_year' => '2024-2025']);
        Schedule::create(['module_id' => 25, 'teacher_id' => 4, 'specialty_id' => 3, 'day' => 'thursday', 'start_time' => '10:00', 'classroom' => 'C102', 'semester' => 1, 'academic_year' => '2024-2025']);
        Schedule::create(['module_id' => 26, 'teacher_id' => 5, 'specialty_id' => 3, 'day' => 'friday', 'start_time' => '08:00', 'classroom' => 'C103', 'semester' => 1, 'academic_year' => '2024-2025']);
    }
}
