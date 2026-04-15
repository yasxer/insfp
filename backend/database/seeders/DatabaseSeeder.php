<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate all tables
        \App\Models\Administration::truncate();
        \App\Models\Attendance::truncate();
        \App\Models\Exam::truncate();
        \App\Models\Grade::truncate();
        \App\Models\Lesson::truncate();
        \App\Models\Message::truncate();
        \App\Models\Notification::truncate();
        \App\Models\Schedule::truncate();
        \App\Models\Student::truncate();
        \App\Models\Teacher::truncate();
        DB::table('teacher_module')->truncate();
        \App\Models\Module::truncate();
        \App\Models\Specialty::truncate();
        \App\Models\User::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call(ITDataSeeder::class);
        $this->call(SessionSpecialtyStudentSeeder::class);
    }
}
