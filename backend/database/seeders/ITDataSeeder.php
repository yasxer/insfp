<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Administration;
use App\Models\Specialty;
use App\Models\Module;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ITDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Admin User
        $adminUser = User::create([
            'email' => 'admin@insfp.dz',
            'phone' => '0500000000',
            'password' => Hash::make('password'),
            'role' => 'administration',
            'is_approved' => true
        ]);
        Administration::create([
            'user_id' => $adminUser->id,
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'position' => 'Director',
        ]);

        // 2. Specialties
        $dev = Specialty::create(['name' => 'Développement Web et Mobile', 'code' => 'DEV', 'duration_semesters' => 4, 'is_active' => true]);
        $db = Specialty::create(['name' => 'Administration Base de Données', 'code' => 'DBA', 'duration_semesters' => 4, 'is_active' => true]);
        $cyber = Specialty::create(['name' => 'Sécurité Informatique', 'code' => 'CYB', 'duration_semesters' => 4, 'is_active' => true]);

        // 3. Modules (S1 and S2 mostly since we are in mid-year)
        $modules = [];
        // DEV
        $modules[] = Module::create(['specialty_id' => $dev->id, 'name' => 'Algorithmique 1', 'code' => 'DEV-S1-ALG', 'semester' => 1, 'coefficient' => 3, 'hours_per_week' => 4]);
        $modules[] = Module::create(['specialty_id' => $dev->id, 'name' => 'Programmation C', 'code' => 'DEV-S1-C', 'semester' => 1, 'coefficient' => 3, 'hours_per_week' => 4]);
        $modules[] = Module::create(['specialty_id' => $dev->id, 'name' => 'PHP & Laravel', 'code' => 'DEV-S2-PHP', 'semester' => 2, 'coefficient' => 4, 'hours_per_week' => 6]);
        $modules[] = Module::create(['specialty_id' => $dev->id, 'name' => 'Vue.js Basics', 'code' => 'DEV-S2-VUE', 'semester' => 2, 'coefficient' => 3, 'hours_per_week' => 4]);

        // DBA
        $modules[] = Module::create(['specialty_id' => $db->id, 'name' => 'Conception BDD', 'code' => 'DBA-S1-CON', 'semester' => 1, 'coefficient' => 4, 'hours_per_week' => 4]);
        $modules[] = Module::create(['specialty_id' => $db->id, 'name' => 'SQL Oracle', 'code' => 'DBA-S1-SQL', 'semester' => 1, 'coefficient' => 3, 'hours_per_week' => 4]);
        $modules[] = Module::create(['specialty_id' => $db->id, 'name' => 'Optimisation PL/SQL', 'code' => 'DBA-S2-PLSQL', 'semester' => 2, 'coefficient' => 4, 'hours_per_week' => 6]);
        $modules[] = Module::create(['specialty_id' => $db->id, 'name' => 'NoSQL MongoDB', 'code' => 'DBA-S2-NOSQL', 'semester' => 2, 'coefficient' => 3, 'hours_per_week' => 4]);

        // CYBER
        $modules[] = Module::create(['specialty_id' => $cyber->id, 'name' => 'Réseaux S1', 'code' => 'CYB-S1-RES', 'semester' => 1, 'coefficient' => 4, 'hours_per_week' => 4]);
        $modules[] = Module::create(['specialty_id' => $cyber->id, 'name' => 'Linux Basics', 'code' => 'CYB-S1-LIN', 'semester' => 1, 'coefficient' => 3, 'hours_per_week' => 4]);
        $modules[] = Module::create(['specialty_id' => $cyber->id, 'name' => 'Hacking Ethique', 'code' => 'CYB-S2-ETH', 'semester' => 2, 'coefficient' => 4, 'hours_per_week' => 6]);
        $modules[] = Module::create(['specialty_id' => $cyber->id, 'name' => 'Cryptographie', 'code' => 'CYB-S2-CRY', 'semester' => 2, 'coefficient' => 3, 'hours_per_week' => 4]);

        // 4. Teachers
        $teacherModels = [];
        for ($i = 1; $i <= 5; $i++) {
            $tu = User::create(['email' => "prof.$i@insfp.dz", 'phone' => "060000000$i", 'password' => Hash::make('password'), 'role' => 'teacher', 'is_approved' => true]);
            $teacherModels[] = Teacher::create([
                'user_id' => $tu->id,
                'first_name' => 'Prof',
                'last_name' => "IT-$i",
                'specialization' => 'IT'
            ]);
        }

        // Attach random teacher to each module
        foreach ($modules as $mod) {
            $randomProf = $teacherModels[array_rand($teacherModels)];
            $mod->teachers()->attach($randomProf->id, ['academic_year' => '2025/2026']);
        }

        // 5. Students (mid year -> current_semester = 2)
        $studentModels = [];
        $specs = [$dev, $db, $cyber];
        for ($i = 1; $i <= 30; $i++) {
            $spec = $specs[array_rand($specs)];
            // Make first 3 students static for easy testing
            $email = $i <= 3 ? "student.$i@insfp.dz" : "stu.$i." . rand(100, 999) . "@insfp.dz";
            $su = User::create(['email' => $email, 'phone' => "07000000$i", 'password' => Hash::make('password'), 'role' => 'student', 'is_approved' => true]);
            $studentModels[] = Student::create([
                'user_id' => $su->id, 'first_name' => 'Student', 'last_name' => "IT-$i", 'date_of_birth' => Carbon::now()->subYears(20),
                'specialty_id' => $spec->id, 'registration_number' => "REG-2026-$i",
                'current_semester' => 2, 'is_graduated' => false
            ]);
        }

        // 6. Exams & Grades for Semester 1 (Past) & Semester 2 Midterm
        $examDateS1Mid = Carbon::parse('2025-11-15');
        $examDateS1Fin = Carbon::parse('2026-01-15');
        $examDateS2Mid = Carbon::parse('2026-04-10'); // recently

        foreach ($modules as $mod) {
            $prof = $mod->teachers()->first();
            $studentsOfSpec = array_filter($studentModels, fn($s) => $s->specialty_id == $mod->specialty_id);

            if ($mod->semester == 1) {
                // S1 Exams (Midterm + Final)
                $mid = Exam::create(['title' => 'Partiel S1', 'exam_type' => 'midterm', 'exam_date' => $examDateS1Mid, 'module_id' => $mod->id, 'specialty_id' => $mod->specialty_id, 'semester' => 1, 'teacher_id' => $prof->id, 'duration_minutes' => 90, 'status' => 'submitted', 'academic_year' => '2025/2026']);
                $fin = Exam::create(['title' => 'Final S1', 'exam_type' => 'final', 'exam_date' => $examDateS1Fin, 'module_id' => $mod->id, 'specialty_id' => $mod->specialty_id, 'semester' => 1, 'teacher_id' => $prof->id, 'duration_minutes' => 120, 'status' => 'submitted', 'academic_year' => '2025/2026']);

                foreach ($studentsOfSpec as $st) {
                    Grade::create(['student_id' => $st->id, 'module_id' => $mod->id, 'exam_id' => $mid->id, 'grade' => rand(8, 19), 'semester' => 1, 'academic_year' => '2025/2026']);
                    Grade::create(['student_id' => $st->id, 'module_id' => $mod->id, 'exam_id' => $fin->id, 'grade' => rand(10, 20), 'semester' => 1, 'academic_year' => '2025/2026']);
                }
            } else if ($mod->semester == 2) {
                // S2 Exam (Midterm)
                $mid = Exam::create(['title' => 'Partiel S2', 'exam_type' => 'midterm', 'exam_date' => $examDateS2Mid, 'module_id' => $mod->id, 'specialty_id' => $mod->specialty_id, 'semester' => 2, 'teacher_id' => $prof->id, 'duration_minutes' => 90, 'status' => 'submitted', 'academic_year' => '2025/2026']);

                foreach ($studentsOfSpec as $st) {
                    Grade::create(['student_id' => $st->id, 'module_id' => $mod->id, 'exam_id' => $mid->id, 'grade' => rand(5, 18), 'semester' => 2, 'academic_year' => '2025/2026']);
                }

                // Add schedules for S2
                $sch = Schedule::create(['specialty_id' => $mod->specialty_id, 'module_id' => $mod->id, 'teacher_id' => $prof->id, 'semester' => 2, 'day' => 'monday', 'start_time' => '08:00:00', 'end_time' => '10:00:00', 'classroom' => 'Lab IT', 'academic_year' => '2025/2026']);

                // Attendance
                $attDate = Carbon::parse('2026-04-13')->format('Y-m-d');
                foreach ($studentsOfSpec as $st) {
                    Attendance::create(['student_id' => $st->id, 'schedule_id' => $sch->id, 'teacher_id' => $prof->id, 'attendance_date' => $attDate, 'status' => (rand(1, 10)>9) ? 'absent' : 'present']);
                }
            }
        }
    }
}
