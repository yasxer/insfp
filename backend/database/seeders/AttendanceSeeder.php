<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Support\Cohorts;
use Database\Seeders\Support\DemoData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Roll calls taken by the teachers over seven weeks of the active period, for
 * every timetabled session. Students flagged 'absent' in the scenario miss
 * about a third of their classes (below the 75 % attendance threshold).
 */
class AttendanceSeeder extends Seeder
{
    private const FROM = '2026-04-05';
    private const TO = '2026-05-21';

    public function run(): void
    {
        $days = ['sunday' => Carbon::SUNDAY, 'monday' => Carbon::MONDAY, 'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY, 'thursday' => Carbon::THURSDAY];
        $sessionId = DB::table('sessions')->where('status', 'active')->value('id');

        foreach (DemoData::COHORTS as $key => $cohort) {
            if (!empty($cohort['graduated'])) {
                continue;
            }
            $students = Cohorts::students($key);
            $absentees = collect(DemoData::PROFILES[$key] ?? [])
                ->filter(fn ($p) => !empty($p['absent']))
                ->map(fn ($p) => "$p[0] $p[1]")->all();

            $schedules = DB::table('schedules')->where('session_id', $sessionId)
                ->where('specialty_id', InstitutionSeeder::specialtyId($cohort['specialty']))
                ->where('semester', $cohort['semester'])
                ->where('study_mode', DemoData::STUDY_MODES[$cohort['type']])
                ->get();

            $rows = [];
            foreach ($schedules as $schedule) {
                $class = $schedule->group ? $students->where('group', $schedule->group) : $students;
                $date = Carbon::parse(self::FROM)->next($days[$schedule->day])->subWeek();
                if ($date->lt(Carbon::parse(self::FROM))) {
                    $date->addWeek();
                }
                for (; $date->lte(Carbon::parse(self::TO)); $date->addWeek()) {
                    $taken = $date->copy()->setTimeFromTimeString($schedule->end_time);
                    foreach ($class as $student) {
                        [$status, $note] = $this->status(in_array("$student->first_name $student->last_name", $absentees, true));
                        $rows[] = [
                            'student_id' => $student->id, 'schedule_id' => $schedule->id,
                            'teacher_id' => $schedule->teacher_id, 'attendance_date' => $date->toDateString(),
                            'status' => $status, 'notes' => $note,
                            'created_at' => $taken, 'updated_at' => $taken,
                        ];
                    }
                }
            }
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('attendances')->insert($chunk);
            }
        }
    }

    private function status(bool $oftenAbsent): array
    {
        $r = mt_rand(1, 100);
        if ($oftenAbsent) {
            return match (true) {
                $r <= 30 => ['absent', null],
                $r <= 38 => ['late', 'Arrivé(e) en retard'],
                default => ['present', null],
            };
        }
        return match (true) {
            $r <= 4 => ['absent', null],
            $r <= 6 => ['excused', 'Certificat médical présenté'],
            $r <= 11 => ['late', 'Arrivé(e) en retard'],
            default => ['present', null],
        };
    }
}
