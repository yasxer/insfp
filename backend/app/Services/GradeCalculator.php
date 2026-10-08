<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Single place where the institute's grading rules are applied, so the student
 * and the administration always see the same averages.
 *
 *   module average   = (2 × C + 2 × E) / 4   (C = mean of the "contrôles", E = "examen")
 *                    = (C1 + C2 + 2E) / 4 with the usual two contrôles
 *   semester average = Σ (module average × coefficient) / Σ coefficients
 *
 * Rattrapage: a student whose semester average is below 10 retakes the modules
 * below 10. The rattrapage mark replaces the examen mark only when it is higher:
 *   E = max(examen, rattrapage)
 *
 * Grades are expected with their `exam` (and `module` for the semester average)
 * relations loaded. Only published grades (exam submitted/modified) count.
 */
class GradeCalculator
{
    public const PUBLISHED_STATUSES = ['submitted', 'modified'];

    /**
     * Average of one module from its grades, or null if nothing is published yet.
     * A redoublant may have grades for the module in several academic years:
     * only the most recent year counts.
     *
     * $withRattrapage = false gives the average before the rattrapage session.
     */
    public function moduleAverage(Collection $grades, bool $withRattrapage = true): ?float
    {
        $grades = $this->latestAcademicYear($this->published($grades));

        $c = $this->mean($grades, 'controle');
        $e = $this->mean($grades, 'examen');
        $before = $this->combine($c, $e);

        $r = $withRattrapage ? $this->mean($grades, 'rattrapage') : null;
        if ($r === null) {
            return $before;
        }

        // The rattrapage replaces the examen only if it is better, so it can never
        // lower the module average.
        $after = $this->combine($c, $e === null ? $r : max($e, $r));

        return $before === null ? $after : max($before, $after);
    }

    /**
     * Module IDs that have a published rattrapage mark among $grades.
     *
     * @return array<int>
     */
    public function modulesWithRattrapage(Collection $grades): array
    {
        return $this->published($grades)
            ->filter(fn ($g) => $g->exam->exam_type === 'rattrapage')
            ->pluck('module_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function combine(?float $c, ?float $e): ?float
    {
        if ($c !== null && $e !== null) {
            return round((2 * $c + 2 * $e) / 4, 2);
        }

        // Only one kind of assessment held so far: it is the module average for now.
        $only = $c ?? $e;

        return $only !== null ? round($only, 2) : null;
    }

    /**
     * Coefficient-weighted average of the modules found in $grades (usually one semester).
     *
     * @return array{average: float, modules: array<int, float>}
     */
    public function semesterAverage(Collection $grades, bool $withRattrapage = true): array
    {
        $grades = $grades->filter(fn ($g) => $g->module);

        $modules = [];
        $totalPoints = 0;
        $totalCoeff = 0;

        foreach ($grades->groupBy('module_id') as $moduleId => $moduleGrades) {
            $average = $this->moduleAverage($moduleGrades, $withRattrapage);
            if ($average === null) {
                continue;
            }

            $coeff = (float) ($moduleGrades->first()->module->coefficient ?: 1);
            $modules[$moduleId] = $average;
            $totalPoints += $average * $coeff;
            $totalCoeff += $coeff;
        }

        return [
            'average' => $totalCoeff > 0 ? round($totalPoints / $totalCoeff, 2) : 0,
            'modules' => $modules,
        ];
    }

    /**
     * Modules a student must retake, from their grades of one semester: none if the
     * semester average (before rattrapage) is >= 10, otherwise every module below 10.
     *
     * @return array<int> module ids
     */
    public function rattrapageModules(Collection $semesterGrades): array
    {
        $before = $this->semesterAverage($semesterGrades, false);

        if (empty($before['modules']) || $before['average'] >= 10) {
            return [];
        }

        return array_keys(array_filter($before['modules'], fn ($avg) => $avg < 10));
    }

    private function mean(Collection $grades, string $type): ?float
    {
        $ofType = $grades->filter(fn ($g) => $g->exam->exam_type === $type);

        return $ofType->isNotEmpty() ? (float) $ofType->avg('grade') : null;
    }

    private function published(Collection $grades): Collection
    {
        return $grades->filter(
            fn ($g) => $g->exam && in_array($g->exam->status, self::PUBLISHED_STATUSES, true)
        );
    }

    /**
     * Keep the most recent academic year of contrôles/examens. A rattrapage sits
     * after those exams and may be recorded under the following academic year,
     * so it counts when its year is the same or later.
     *
     * Academic years are stored as "2025-2026" or "2025/2026": compare on the start year.
     */
    private function latestAcademicYear(Collection $grades): Collection
    {
        $startYear = fn ($g) => (int) substr((string) $g->academic_year, 0, 4);
        $isRattrapage = fn ($g) => $g->exam->exam_type === 'rattrapage';

        $regular = $grades->reject($isRattrapage);
        $latest = ($regular->isNotEmpty() ? $regular : $grades)->max($startYear);

        return $grades->filter(fn ($g) => $isRattrapage($g)
            ? $startYear($g) >= $latest
            : $startYear($g) === $latest);
    }
}
