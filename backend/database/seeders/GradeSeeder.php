<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Exam;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::where('current_semester', 1)->get();
        $exams = Exam::where('semester', 1)
                    ->where('exam_type', 'midterm')
                    ->get();

        if ($students->isEmpty() || $exams->isEmpty()) {
            $this->command->warn('No students or exams found for semester 1');
            return;
        }

        $academicYear = '2024-2025';
        $gradeCount = 0;

        foreach ($students as $student) {
            // Get exams for this student's specialty
            $studentExams = $exams->where('specialty_id', $student->specialty_id);

            if ($studentExams->isEmpty()) {
                continue;
            }

            // Create grades for 70% of exams (simulate some students haven't taken all exams yet)
            $examsToGrade = $studentExams->random(min(ceil($studentExams->count() * 0.7), $studentExams->count()));

            foreach ($examsToGrade as $exam) {
                // Check if grade already exists
                if (Grade::where('student_id', $student->id)
                    ->where('exam_id', $exam->id)
                    ->exists()) {
                    continue;
                }

                // Generate realistic grade distribution
                $rand = rand(1, 100);
                if ($rand <= 10) {
                    // 10% excellent (16-20)
                    $grade = rand(1600, 2000) / 100;
                } elseif ($rand <= 30) {
                    // 20% very good (14-16)
                    $grade = rand(1400, 1600) / 100;
                } elseif ($rand <= 60) {
                    // 30% good (12-14)
                    $grade = rand(1200, 1400) / 100;
                } elseif ($rand <= 80) {
                    // 20% pass (10-12)
                    $grade = rand(1000, 1200) / 100;
                } else {
                    // 20% fail (5-10)
                    $grade = rand(500, 1000) / 100;
                }

                Grade::create([
                    'student_id' => $student->id,
                    'module_id' => $exam->module_id,
                    'exam_id' => $exam->id,
                    'grade' => $grade,
                    'semester' => 1,
                    'academic_year' => $academicYear,
                ]);

                $gradeCount++;
            }
        }

        $this->command->info("Created {$gradeCount} grade records");
    }
}
