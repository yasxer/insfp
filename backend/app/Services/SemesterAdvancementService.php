<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Deliberation;
use App\Models\AdvancementReview;
use Illuminate\Support\Facades\DB;

/**
 * Advances students one semester based on their latest deliberation for their
 * current semester:
 *   - passed (moyenne >= 10)  -> next semester, or graduate if it was the last one
 *   - failed (moyenne < 10)   -> a pending review row (admin decides case by case)
 *   - no deliberation         -> left untouched (new / not-yet-deliberated students)
 *
 * The whole process is idempotent: advancing changes current_semester, so a
 * student without a fresh deliberation for the NEW semester won't advance again,
 * and review rows are guarded by a unique (student, semester, academic_year) key.
 */
class SemesterAdvancementService
{
    public function run(): array
    {
        $summary = ['advanced' => 0, 'graduated' => 0, 'review' => 0, 'skipped' => 0];

        $students = Student::where('is_graduated', false)
            ->where('is_excluded', false)
            ->whereNotNull('current_semester')
            ->with('specialty')
            ->get();

        DB::transaction(function () use ($students, &$summary) {
            foreach ($students as $student) {
                $delib = Deliberation::where('student_id', $student->id)
                    ->where('semester', $student->current_semester)
                    ->orderByDesc('academic_year')
                    ->orderByDesc('id')
                    ->first();

                if (!$delib) {
                    $summary['skipped']++;
                    continue;
                }

                if ($delib->result === 'passed') {
                    $maxSemester = $student->specialty->duration_semesters ?? 6;

                    if ($student->current_semester >= $maxSemester) {
                        $student->update([
                            'is_graduated'        => true,
                            'graduation_year'     => (int) substr($delib->academic_year, 0, 4),
                            'graduation_semester' => $student->current_semester,
                            'final_gpa'           => $delib->average,
                        ]);
                        $summary['graduated']++;
                    } else {
                        $student->increment('current_semester');
                        $summary['advanced']++;
                    }
                } else {
                    AdvancementReview::firstOrCreate(
                        [
                            'student_id'    => $student->id,
                            'semester'      => $student->current_semester,
                            'academic_year' => $delib->academic_year,
                        ],
                        [
                            'average' => $delib->average,
                            'status'  => 'pending',
                        ]
                    );
                    $summary['review']++;
                }
            }
        });

        return $summary;
    }
}
