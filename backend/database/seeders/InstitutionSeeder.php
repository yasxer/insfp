<?php

namespace Database\Seeders;

use App\Models\TrainingSession;
use Database\Seeders\Support\DemoData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Administration account, specialties with their 5-semester programme, teachers
 * and their modules, sessions and the specialties each intake offers.
 */
class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $password = Hash::make(DemoData::PASSWORD);

        // ── Administration ───────────────────────────────────────────
        $adminUserId = DB::table('users')->insertGetId([
            'email' => 'admin@insfp.dz', 'first_name' => 'Nadia', 'last_name' => 'Hamidi',
            'phone' => '0550123456', 'password' => $password, 'role' => 'administration',
            'is_approved' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('administrations')->insert([
            'user_id' => $adminUserId, 'first_name' => 'Nadia', 'last_name' => 'Hamidi',
            'position' => 'Directrice des études', 'created_at' => $now, 'updated_at' => $now,
        ]);

        // ── Teachers ─────────────────────────────────────────────────
        $teacherIds = [];
        $i = 0;
        foreach (DemoData::TEACHERS as $key => [$first, $last, $specialization]) {
            $i++;
            $userId = DB::table('users')->insertGetId([
                'email' => DemoData::slug($first) . '.' . DemoData::slug($last) . '@insfp.dz',
                'first_name' => $first, 'last_name' => $last,
                'phone' => '06610' . str_pad((string) (10000 + $i * 731), 5, '0', STR_PAD_LEFT),
                'password' => $password, 'role' => 'teacher', 'is_approved' => true,
                'created_at' => $now->copy()->subYears(3), 'updated_at' => $now,
            ]);
            $teacherIds[$key] = DB::table('teachers')->insertGetId([
                'user_id' => $userId, 'first_name' => $first, 'last_name' => $last,
                'specialization' => $specialization, 'created_at' => $now->copy()->subYears(3), 'updated_at' => $now,
            ]);
        }

        // ── Specialties, modules and teacher assignments ─────────────
        foreach (DemoData::specialties() as $code => $spec) {
            $specialtyId = DB::table('specialties')->insertGetId([
                'name' => $spec['name'], 'code' => $code, 'study_mode' => $spec['study_mode'],
                'description' => $spec['description'], 'current_semester' => 1,
                'duration_years' => 2.5, 'duration_semesters' => 5, 'is_active' => true,
                'created_at' => $now->copy()->subYears(4), 'updated_at' => $now,
            ]);

            foreach ($spec['modules'] as $semester => $modules) {
                foreach ($modules as [$suffix, $name, $coef, $slots, $teacher]) {
                    $moduleId = DB::table('modules')->insertGetId([
                        'specialty_id' => $specialtyId, 'name' => $name,
                        'code' => "$code-S$semester-$suffix",
                        'description' => "Module du semestre $semester — {$spec['name']}.",
                        'semester' => $semester, 'coefficient' => $coef,
                        'hours_per_week' => max(1, (int) round($slots * 1.5)),
                        'created_at' => $now->copy()->subYears(4), 'updated_at' => $now,
                    ]);
                    DB::table('teacher_module')->insert([
                        'teacher_id' => $teacherIds[$teacher], 'module_id' => $moduleId,
                        'academic_year' => DemoData::CURRENT_YEAR, 'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }
        }

        // ── Sessions (the model computes dates and the name) ─────────
        foreach (DemoData::SESSIONS as $s) {
            $session = TrainingSession::create([
                'month' => $s['month'], 'year' => $s['year'],
                'status' => $s['status'], 'is_active' => $s['status'] === 'active',
            ]);
            $session->forceFill(['created_at' => $session->start_date->copy()->subMonths(2)])->save();
        }

        // ── Specialties offered by each intake ───────────────────────
        $offers = [];
        foreach (DemoData::COHORTS as $cohort) {
            $offers[] = [$cohort['session'], $cohort['specialty'], $cohort['type']];
        }
        foreach (DemoData::NEXT_INTAKE as [$code, $type]) {
            $offers[] = [[9, 2026], $code, $type];
        }
        foreach ($offers as [[$month, $year], $code, $type]) {
            DB::table('session_specialties')->insertOrIgnore([
                'session_id' => self::sessionId($month, $year),
                'specialty_id' => self::specialtyId($code),
                'study_type' => $type, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public static function sessionId(int $month, int $year): int
    {
        return (int) DB::table('sessions')->where('month', $month)->where('year', $year)->value('id');
    }

    public static function specialtyId(string $code): int
    {
        return (int) DB::table('specialties')->where('code', $code)->value('id');
    }
}
