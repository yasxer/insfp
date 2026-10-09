<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo data for the INSFP platform (see Support\DemoData for the scenario).
 *
 *   php artisan migrate:fresh --seed
 *
 * Every account uses the password "password".
 */
class DatabaseSeeder extends Seeder
{
    private const TABLES = [
        'advancement_reviews', 'attendances', 'cache', 'cache_locks', 'deliberations', 'documents',
        'encadrement_appointments', 'encadrement_students', 'encadrement_groups', 'exams', 'grades',
        'holidays', 'homework_submissions', 'homework', 'lessons', 'message_reads', 'messages',
        'notifications', 'personal_access_tokens', 'registration_numbers', 'schedule_statuses', 'schedules',
        'session_specialties', 'sessions', 'students', 'teacher_module', 'modules', 'teachers',
        'administrations', 'specialties', 'users',
    ];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach (self::TABLES as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Same data on every run
        mt_srand(2026);

        $this->call([
            InstitutionSeeder::class,
            CohortSeeder::class,
            TimetableSeeder::class,
            EvaluationSeeder::class,
            AttendanceSeeder::class,
            ContentSeeder::class,
        ]);
    }
}
