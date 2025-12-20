<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Teacher 1 (Ahmed Bennani - Informatique) → DD modules
        DB::table('teacher_module')->insert([
            ['teacher_id' => 1, 'module_id' => 1, 'academic_year' => '2024-2025'], // DD-ALG
            ['teacher_id' => 1, 'module_id' => 2, 'academic_year' => '2024-2025'], // DD-WEB
            ['teacher_id' => 1, 'module_id' => 3, 'academic_year' => '2024-2025'], // DD-BDD
            ['teacher_id' => 1, 'module_id' => 5, 'academic_year' => '2024-2025'], // DD-JAV
            ['teacher_id' => 1, 'module_id' => 9, 'academic_year' => '2024-2025'], // DD-LAR
        ]);

        // Teacher 2 (Fatima Zahra - Management) → GE modules
        DB::table('teacher_module')->insert([
            ['teacher_id' => 2, 'module_id' => 14, 'academic_year' => '2024-2025'], // GE-MNG
            ['teacher_id' => 2, 'module_id' => 15, 'academic_year' => '2024-2025'], // GE-MKT
            ['teacher_id' => 2, 'module_id' => 17, 'academic_year' => '2024-2025'], // GE-GRH
        ]);

        // Teacher 3 (Youssef Amrani - Mathématiques) → Statistics
        DB::table('teacher_module')->insert([
            ['teacher_id' => 3, 'module_id' => 20, 'academic_year' => '2024-2025'], // GE-STA
        ]);

        // Teacher 4 (Khadija El Fassi - Comptabilité) → CF + GE comptabilité
        DB::table('teacher_module')->insert([
            ['teacher_id' => 4, 'module_id' => 13, 'academic_year' => '2024-2025'], // GE-CG1
            ['teacher_id' => 4, 'module_id' => 23, 'academic_year' => '2024-2025'], // CF-CG1
            ['teacher_id' => 4, 'module_id' => 24, 'academic_year' => '2024-2025'], // CF-FIS
            ['teacher_id' => 4, 'module_id' => 27, 'academic_year' => '2024-2025'], // CF-CAN
            ['teacher_id' => 4, 'module_id' => 28, 'academic_year' => '2024-2025'], // CF-AUD
        ]);

        // Teacher 5 (Omar Idrissi - Anglais) → All English modules
        DB::table('teacher_module')->insert([
            ['teacher_id' => 5, 'module_id' => 4, 'academic_year' => '2024-2025'],  // DD-ANG1
            ['teacher_id' => 5, 'module_id' => 8, 'academic_year' => '2024-2025'],  // DD-ANG2
            ['teacher_id' => 5, 'module_id' => 16, 'academic_year' => '2024-2025'], // GE-ANG1
            ['teacher_id' => 5, 'module_id' => 26, 'academic_year' => '2024-2025'], // CF-ANG1
        ]);
    }
}
