<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Module;
use App\Models\Specialty;
use Carbon\Carbon;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = Specialty::all();
        $academicYear = '2024-2025';
        $semester = 1;

        if ($specialties->isEmpty()) {
            $this->command->warn('No specialties found');
            return;
        }

        $examCount = 0;
        $classrooms = ['A101', 'A102', 'A103', 'B201', 'B202', 'B203', 'Amphi 1', 'Amphi 2'];

        foreach ($specialties as $specialty) {
            // Get modules for this specialty
            $modules = Module::where('specialty_id', $specialty->id)
                            ->where('semester', $semester)
                            ->get();

            if ($modules->isEmpty()) {
                continue;
            }

            foreach ($modules as $module) {
                // Create midterm exam (2 weeks from now)
                Exam::create([
                    'module_id' => $module->id,
                    'specialty_id' => $specialty->id,
                    'exam_type' => 'midterm',
                    'exam_date' => Carbon::now()->addDays(14)->setTime(8, 30),
                    'duration_minutes' => 90,
                    'classroom' => $classrooms[array_rand($classrooms)],
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                ]);
                $examCount++;

                // Create final exam (6 weeks from now)
                Exam::create([
                    'module_id' => $module->id,
                    'specialty_id' => $specialty->id,
                    'exam_type' => 'final',
                    'exam_date' => Carbon::now()->addDays(42)->setTime(8, 30),
                    'duration_minutes' => 120,
                    'classroom' => $classrooms[array_rand($classrooms)],
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                ]);
                $examCount++;

                // 30% chance of rattrapage exam (10 weeks from now)
                if (rand(1, 100) <= 30) {
                    Exam::create([
                        'module_id' => $module->id,
                        'specialty_id' => $specialty->id,
                        'exam_type' => 'rattrapage',
                        'exam_date' => Carbon::now()->addDays(70)->setTime(8, 30),
                        'duration_minutes' => 120,
                        'classroom' => $classrooms[array_rand($classrooms)],
                        'semester' => $semester,
                        'academic_year' => $academicYear,
                    ]);
                    $examCount++;
                }
            }
        }

        $this->command->info("Created {$examCount} exam records");
    }
}
