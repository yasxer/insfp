<?php

namespace Database\Seeders;

use App\Services\GradeCalculator;
use Carbon\Carbon;
use Database\Seeders\Support\Cohorts;
use Database\Seeders\Support\DemoData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Exams and grades of every semester followed by each cohort, the rattrapage
 * session, and the deliberations already held.
 *
 * Every grade goes through the platform's GradeCalculator so the seeded averages
 * are exactly what the deliberation page will show.
 */
class EvaluationSeeder extends Seeder
{
    /** Cohorts whose current semester is already deliberated. */
    private const DELIBERATED_NOW = ['BDD24', 'MNT25'];

    private GradeCalculator $calculator;
    private array $levels = [];

    public function run(): void
    {
        $this->calculator = app(GradeCalculator::class);

        foreach (DemoData::COHORTS as $key => $cohort) {
            if (!empty($cohort['graduated'])) {
                $this->graduatedHistory($key);
                continue;
            }

            $students = Cohorts::students($key);
            $profiles = collect(DemoData::PROFILES[$key] ?? [])->keyBy(fn ($p) => "$p[0] $p[1]");

            for ($k = 1; $k <= $cohort['semester']; $k++) {
                $current = $k === $cohort['semester'];
                $grades = $this->semester($key, $k, $students, $current ? $profiles : collect());

                if ($current && $key === 'DWM25') {
                    $this->rattrapage($key, $k, $students, $profiles, $grades, true);
                } elseif ($current && $key === 'ASR25') {
                    $this->rattrapage($key, $k, $students, $profiles, $grades, false);
                }

                if (!$current || in_array($key, self::DELIBERATED_NOW, true)) {
                    $this->deliberate($key, $k, $students, $grades);
                }
            }
        }
    }

    /**
     * Two contrôles and one examen per module. Returns every grade of the
     * semester as objects the GradeCalculator understands.
     */
    private function semester(string $key, int $k, Collection $students, Collection $profiles): Collection
    {
        $code = DemoData::COHORTS[$key]['specialty'];
        $start = Cohorts::semesterStart($key, $k);
        $year = DemoData::academicYear($start);
        $modules = Cohorts::modules($code, $k);
        $sessions = [
            ['controle', 'Contrôle 1', 6, 90],
            ['controle', 'Contrôle 2', 13, 90],
            ['examen', 'Examen final', 19, 120],
        ];

        $all = collect();
        foreach ($sessions as [$type, $label, $week, $minutes]) {
            $m = 0;
            foreach ($modules as $suffix => $module) {
                $date = Cohorts::teachingDay($start->copy()->addWeeks($week)->addDays($m++))->setTime(9, 0);
                // One exam still waiting for the teacher to submit it (SEC, Python)
                $status = ($key === 'SEC26' && $suffix === 'PYT' && $type === 'examen') ? 'draft' : 'submitted';
                $exam = $this->exam($module, $code, $type, "$label — {$module->name}", $date, $minutes, $k, $year, $status);

                $rows = [];
                foreach ($students as $student) {
                    $planned = $profiles->get("$student->first_name $student->last_name")['grades'][$suffix] ?? null;
                    $mark = $planned !== null
                        ? $planned + ($label === 'Contrôle 1' ? 0.5 : ($label === 'Contrôle 2' ? -0.5 : 0))
                        : $this->mark($student->id, $module->id);
                    $rows[] = $this->gradeRow($student->id, $module, $exam, $mark, $k, $year, $date);
                }
                $all = $all->concat($this->insertGrades($rows, $exam, $module));
            }
        }

        // An ordinary student never falls below 10 by accident: only the
        // scenario profiles go to the rattrapage.
        foreach ($students as $student) {
            if ($profiles->has("$student->first_name $student->last_name")) {
                continue;
            }
            $own = $all->where('student_id', $student->id);
            $avg = $this->calculator->semesterAverage($own, false)['average'];
            if ($avg < 10.5) {
                $shift = round((10.75 - $avg) * 4) / 4;
                foreach ($own as $g) {
                    $g->grade = min(19.75, $g->grade + $shift);
                    DB::table('grades')->where('id', $g->id)->update(['grade' => $g->grade]);
                }
            }
        }

        return $all;
    }

    /**
     * Rattrapage of the modules below 10 for students whose semester average is
     * below 10. DWM: held and published. ASR: planned, to be graded live.
     */
    private function rattrapage(string $key, int $k, Collection $students, Collection $profiles, Collection $grades, bool $graded): void
    {
        $code = DemoData::COHORTS[$key]['specialty'];
        $modules = Cohorts::modules($code, $k);
        $year = DemoData::academicYear(Cohorts::semesterStart($key, $k));

        $toRetake = [];
        foreach ($students as $student) {
            foreach ($this->calculator->rattrapageModules($grades->where('student_id', $student->id)) as $moduleId) {
                $toRetake[$moduleId][] = $student;
            }
        }

        $day = 0;
        foreach ($modules as $suffix => $module) {
            if (empty($toRetake[$module->id])) {
                continue;
            }
            $date = $graded
                ? Cohorts::teachingDay(Carbon::create(2026, 9, 20)->addDays($day++))->setTime(9, 0)
                : Cohorts::teachingDay(Carbon::create(2026, 10, 12)->addDays($day++))->setTime(9, 0);
            $exam = $this->exam($module, $code, 'rattrapage', "Rattrapage — {$module->name}", $date, 120, $k, $year,
                $graded ? 'submitted' : 'draft');

            if (!$graded) {
                continue;
            }
            $rows = [];
            foreach ($toRetake[$module->id] as $student) {
                $mark = $profiles->get("$student->first_name $student->last_name")['rattrapage'][$suffix] ?? 10;
                $rows[] = $this->gradeRow($student->id, $module, $exam, $mark, $k, $year, $date);
            }
            $grades->push(...$this->insertGrades($rows, $exam, $module));
        }
    }

    private function deliberate(string $key, int $k, Collection $students, Collection $grades): void
    {
        $start = Cohorts::semesterStart($key, $k);
        $date = $start->copy()->addMonths(5)->addDays(5);
        foreach ($students as $student) {
            $average = $this->calculator->semesterAverage($grades->where('student_id', $student->id))['average'];
            DB::table('deliberations')->insert([
                'student_id' => $student->id, 'semester' => $k,
                'academic_year' => DemoData::academicYear($start),
                'average' => $average, 'result' => $average >= 10 ? 'passed' : 'failed',
                'observations' => $average >= 10 ? self::mention($average) : 'Ajourné(e)',
                'deliberation_date' => $date->toDateString(),
                'created_at' => $date, 'updated_at' => $date,
            ]);
        }
    }

    /** Graduates: deliberations of their five semesters and the final average. */
    private function graduatedHistory(string $key): void
    {
        foreach (Cohorts::students($key) as $student) {
            $averages = [];
            for ($k = 1; $k <= 5; $k++) {
                $start = Cohorts::semesterStart($key, $k);
                $date = $start->copy()->addMonths(5)->addDays(5);
                $average = $averages[] = mt_rand(1080, 1620) / 100;
                DB::table('deliberations')->insert([
                    'student_id' => $student->id, 'semester' => $k,
                    'academic_year' => DemoData::academicYear($start),
                    'average' => $average, 'result' => 'passed', 'observations' => self::mention($average),
                    'deliberation_date' => $date->toDateString(), 'created_at' => $date, 'updated_at' => $date,
                ]);
            }
            DB::table('students')->where('id', $student->id)
                ->update(['final_gpa' => round(array_sum($averages) / 5, 2)]);
        }
    }

    private function exam(object $module, string $code, string $type, string $title, Carbon $date, int $minutes, int $semester, string $year, string $status): int
    {
        return DB::table('exams')->insertGetId([
            'title' => $title, 'module_id' => $module->id,
            'specialty_id' => InstitutionSeeder::specialtyId($code), 'teacher_id' => $module->teacher_id,
            'exam_type' => $type, 'status' => $status, 'exam_date' => $date,
            'duration_minutes' => $minutes,
            'classroom' => DemoData::ROOMS[$module->room_type][0],
            'semester' => $semester, 'group' => null, 'academic_year' => $year,
            'created_at' => $date->copy()->subWeeks(2), 'updated_at' => $date->copy()->addDays(6),
        ]);
    }

    private function gradeRow(int $studentId, object $module, int $examId, float $mark, int $semester, string $year, Carbon $date): array
    {
        return [
            'student_id' => $studentId, 'module_id' => $module->id, 'exam_id' => $examId,
            'grade' => max(0, min(20, $mark)), 'semester' => $semester, 'academic_year' => $year,
            'created_at' => $date->copy()->addDays(6), 'updated_at' => $date->copy()->addDays(6),
        ];
    }

    /** Inserts grades and returns them in the shape the GradeCalculator expects. */
    private function insertGrades(array $rows, int $examId, object $module): Collection
    {
        if (!$rows) {
            return collect();
        }
        DB::table('grades')->insert($rows);
        $exam = DB::table('exams')->find($examId);

        return DB::table('grades')->where('exam_id', $examId)->get()->map(function ($g) use ($exam, $module) {
            $g->grade = (float) $g->grade;
            $g->exam = $exam;
            $g->module = $module;
            return $g;
        });
    }

    /** Mark around the student's level, rounded to a quarter point. */
    private function mark(int $studentId, int $moduleId): float
    {
        $level = $this->levels[$studentId] ??= mt_rand(1050, 1650) / 100;
        $moduleShift = (($moduleId * 7 + $studentId * 3) % 7 - 3) / 2;   // stable per student/module
        $mark = $level + $moduleShift + mt_rand(-200, 200) / 100;
        return max(3, min(19.75, round($mark * 4) / 4));
    }

    private static function mention(float $average): string
    {
        return match (true) {
            $average >= 16 => 'Admis(e) — mention Très bien',
            $average >= 14 => 'Admis(e) — mention Bien',
            $average >= 12 => 'Admis(e) — mention Assez bien',
            default => 'Admis(e) — mention Passable',
        };
    }
}
