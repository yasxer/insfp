<?php

namespace Database\Seeders;

use Database\Seeders\Support\DemoData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Weekly timetable of the active period for every cohort still in training,
 * built without clashes: a teacher, a class (or group) and a room are never
 * booked twice in the same slot. Multi-group cohorts get one practical session
 * per group; apprentices come to the institute two days a week.
 */
class TimetableSeeder extends Seeder
{
    private array $teacherBusy = [];
    private array $classBusy = [];
    private array $roomBusy = [];

    public function run(): void
    {
        $now = now();
        $sessionId = (int) DB::table('sessions')->where('status', 'active')->value('id');
        $specialties = DemoData::specialties();
        $teacherIds = DB::table('teachers')->pluck('id', 'last_name')
            ->mapWithKeys(fn ($id, $last) => [DemoData::slug($last) => $id]);

        foreach (DemoData::COHORTS as $key => $cohort) {
            if (!empty($cohort['graduated'])) {
                continue;
            }
            $code = $cohort['specialty'];
            $semester = $cohort['semester'];
            $specialtyId = InstitutionSeeder::specialtyId($code);
            $studyMode = DemoData::STUDY_MODES[$cohort['type']];
            $groups = $cohort['groups'] ?? ['G1'];
            $days = $cohort['type'] === 'apprentissage' ? DemoData::APPRENTICE_DAYS : DemoData::DAYS;

            // Units to place: one per weekly slot of each module
            $units = [];
            $splitDone = count($groups) < 2;
            foreach ($specialties[$code]['modules'][$semester] as $m => [$suffix, , , $slots, $teacher, $roomType]) {
                $moduleId = (int) DB::table('modules')->where('code', "$code-S$semester-$suffix")->value('id');
                for ($k = 0; $k < $slots; $k++) {
                    $units[] = compact('moduleId', 'teacher', 'roomType', 'm') + ['group' => null];
                }
                // First lab module of a multi-group cohort: one practical session per group
                if (!$splitDone && $roomType !== 'salle') {
                    foreach ($groups as $g) {
                        $units[] = compact('moduleId', 'teacher', 'roomType', 'm') + ['group' => $g];
                    }
                    $splitDone = true;
                }
            }

            foreach ($units as $u => $unit) {
                [$day, $slot, $room] = $this->place($key, $unit, $days, $groups, $teacherIds[$unit['teacher']], $u);
                DB::table('schedules')->insert([
                    'session_id' => $sessionId, 'module_id' => $unit['moduleId'],
                    'teacher_id' => $teacherIds[$unit['teacher']], 'specialty_id' => $specialtyId,
                    'study_mode' => $studyMode, 'group' => $unit['group'], 'day' => $day,
                    'start_time' => DemoData::SLOTS[$slot][0] . ':00', 'end_time' => DemoData::SLOTS[$slot][1] . ':00',
                    'classroom' => $room, 'semester' => $semester, 'academic_year' => DemoData::CURRENT_YEAR,
                    'created_at' => '2026-01-25 09:00:00', 'updated_at' => $now,
                ]);
            }

            // Timetable finalised and published by the administration
            foreach ($groups as $g) {
                DB::table('schedule_statuses')->insert([
                    'session_id' => $sessionId, 'specialty_id' => $specialtyId, 'semester' => $semester,
                    'study_mode' => $studyMode, 'group' => $g, 'is_published' => true,
                    'created_at' => '2026-01-28 10:00:00', 'updated_at' => '2026-01-28 10:00:00',
                ]);
            }
        }
    }

    /** First free (day, slot, room), spreading a module over different days. */
    private function place(string $cohort, array $unit, array $days, array $groups, int $teacherId, int $index): array
    {
        $nDays = count($days);
        // Try lighter days first, starting from a different day for each module
        $order = [];
        for ($d = 0; $d < $nDays; $d++) {
            $order[] = $days[($d + $unit['m'] * 2 + $index) % $nDays];
        }

        foreach ([true, false] as $spread) {
            foreach ($order as $day) {
                if ($spread && (isset($this->classBusy["$cohort|$day|module{$unit['moduleId']}{$unit['group']}"])
                    || $this->dayLoad($cohort, $day) >= 3)) {
                    continue;
                }
                foreach (array_keys(DemoData::SLOTS) as $slot) {
                    if (isset($this->teacherBusy["$teacherId|$day|$slot"]) || !$this->classFree($cohort, $day, $slot, $unit['group'], $groups)) {
                        continue;
                    }
                    foreach (DemoData::ROOMS[$unit['roomType']] as $room) {
                        if (isset($this->roomBusy["$room|$day|$slot"])) {
                            continue;
                        }
                        $this->teacherBusy["$teacherId|$day|$slot"] = true;
                        $this->roomBusy["$room|$day|$slot"] = true;
                        $this->classBusy["$cohort|$day|$slot|" . ($unit['group'] ?? '*')] = true;
                        $this->classBusy["$cohort|$day|module{$unit['moduleId']}{$unit['group']}"] = true;
                        $this->classBusy["$cohort|$day|load"] = ($this->classBusy["$cohort|$day|load"] ?? 0) + 1;
                        return [$day, $slot, $room];
                    }
                }
            }
        }
        throw new RuntimeException("Emploi du temps impossible pour $cohort (module {$unit['moduleId']}).");
    }

    private function classFree(string $cohort, string $day, int $slot, ?string $group, array $groups): bool
    {
        if (isset($this->classBusy["$cohort|$day|$slot|*"])) {
            return false;
        }
        // A whole-class session needs every group free; a group session only its own group
        foreach ($group ? [$group] : $groups as $g) {
            if (isset($this->classBusy["$cohort|$day|$slot|$g"])) {
                return false;
            }
        }
        return true;
    }

    private function dayLoad(string $cohort, string $day): int
    {
        return $this->classBusy["$cohort|$day|load"] ?? 0;
    }
}
