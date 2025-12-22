<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Module;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TeacherGradesController extends Controller
{
    /**
     * List exams for this teacher
     */
    public function exams(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Teacher profile not found'], 404);
        }

        // Get module IDs for this teacher
        $moduleIds = $teacher->modules()->pluck('modules.id');

        $query = Exam::whereIn('module_id', $moduleIds)
            ->with('module');

        if ($request->module_id) {
            $query->where('module_id', $request->module_id);
        }

        if ($request->from) {
            $query->whereDate('exam_date', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('exam_date', '<=', $request->to);
        }
        if ($request->upcoming) {
            $query->where('exam_date', '>=', now());
        }

        // Eager load grades count
        $query->withCount('grades as results_count');

        $exams = $query->orderBy('exam_date', 'desc')->paginate(10);

        // Transform
        $exams->getCollection()->transform(function ($exam) {
            return [
                'id' => $exam->id,
                'title' => ucfirst($exam->exam_type) . ' Exam',
                'type' => $exam->exam_type,
                'date' => $exam->exam_date->format('Y-m-d'),
                'max_mark' => 20,
                'module' => [
                    'id' => $exam->module->id,
                    'code' => $exam->module->code,
                    'name' => $exam->module->name,
                ],
                'results_count' => $exam->results_count,
            ];
        });

        return response()->json($exams);
    }

    /**
     * Get students for one exam + current marks
     */
    public function examStudents(Exam $exam): JsonResponse
    {
        $teacher = request()->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Teacher profile not found'], 404);
        }

        // Authorization: Check if teacher is assigned to the module
        if (!$teacher->modules()->where('modules.id', $exam->module_id)->exists()) {
            return response()->json(['message' => 'Unauthorized access to this exam'], 403);
        }

        $module = $exam->module;

        // Get students for this module (Specialty match)
        $students = Student::where('specialty_id', $module->specialty_id)
            ->with('user') // Eager load user for email
            ->orderBy('last_name')
            ->get();

        // Get existing grades for this exam
        $grades = Grade::where('exam_id', $exam->id)->get()->keyBy('student_id');

        $studentList = $students->map(function ($student) use ($grades) {
            $grade = $grades->get($student->id);
            $mark = $grade ? (float)$grade->grade : null;
            return [
                'id' => $student->id,
                'registration_number' => $student->registration_number,
                'full_name' => $student->full_name,
                'email' => $student->user->email ?? null,
                'mark' => $mark,
                'grade_letter' => $mark !== null ? $this->calculateGradeLetter($mark) : null,
            ];
        });

        return response()->json([
            'exam' => [
                'id' => $exam->id,
                'title' => ucfirst($exam->exam_type) . ' Exam',
                'type' => $exam->exam_type,
                'date' => $exam->exam_date->format('Y-m-d'),
                'max_mark' => 20,
                'module' => [
                    'id' => $module->id,
                    'code' => $module->code,
                    'name' => $module->name,
                ]
            ],
            'students' => $studentList
        ]);
    }

    /**
     * Create/update marks for an exam
     */
    public function storeResults(Request $request, Exam $exam): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Teacher profile not found'], 404);
        }

        if (!$teacher->modules()->where('modules.id', $exam->module_id)->exists()) {
            return response()->json(['message' => 'Unauthorized access to this exam'], 403);
        }

        $validated = $request->validate([
            'results' => 'required|array|min:1',
            'results.*.student_id' => 'required|exists:students,id',
            'results.*.mark' => 'required|numeric|min:0|max:20',
            'results.*.note' => 'nullable|string|max:255',
        ]);

        $updatedStudents = [];

        DB::transaction(function () use ($exam, $validated, &$updatedStudents) {
            foreach ($validated['results'] as $result) {
                // Verify student belongs to the module's specialty (optional but good practice)
                // For now, we assume the frontend sends valid student IDs from the examStudents list.

                Grade::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $result['student_id'],
                    ],
                    [
                        'module_id' => $exam->module_id,
                        'grade' => $result['mark'],
                        'semester' => $exam->semester,
                        'academic_year' => $exam->academic_year,
                        // 'note' => $result['note'] // Note column does not exist in grades table
                    ]
                );

                $student = Student::find($result['student_id']);
                if ($student) {
                    $updatedStudents[] = [
                        'id' => $student->id,
                        'registration_number' => $student->registration_number,
                        'full_name' => $student->full_name,
                        'mark' => $result['mark'],
                        'grade_letter' => $this->calculateGradeLetter($result['mark']),
                    ];
                }
            }
        });

        return response()->json([
            'message' => 'Exam results saved successfully.',
            'exam_id' => $exam->id,
            'students' => $updatedStudents
        ]);
    }

    /**
     * Exam results summary for this teacher
     */
    public function history(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Teacher profile not found'], 404);
        }

        $moduleIds = $teacher->modules()->pluck('modules.id');

        $query = Exam::whereIn('module_id', $moduleIds)
            ->whereHas('grades') // Only exams with grades
            ->with(['module', 'grades']);

        if ($request->module_id) {
            $query->where('module_id', $request->module_id);
        }
        if ($request->from) {
            $query->whereDate('exam_date', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('exam_date', '<=', $request->to);
        }

        $exams = $query->orderBy('exam_date', 'desc')->paginate(10);

        $exams->getCollection()->transform(function ($exam) {
            $grades = $exam->grades;
            $count = $grades->count();
            $avg = $count > 0 ? $grades->avg('grade') : 0;
            $min = $count > 0 ? $grades->min('grade') : 0;
            $max = $count > 0 ? $grades->max('grade') : 0;

            return [
                'exam_id' => $exam->id,
                'title' => ucfirst($exam->exam_type) . ' Exam',
                'type' => $exam->exam_type,
                'date' => $exam->exam_date->format('Y-m-d'),
                'module' => [
                    'id' => $exam->module->id,
                    'code' => $exam->module->code,
                    'name' => $exam->module->name,
                ],
                'max_mark' => 20,
                'students_count' => $count,
                'avg_mark' => round($avg, 2),
                'min_mark' => $min,
                'max_mark_obtained' => $max,
            ];
        });

        return response()->json($exams);
    }

    /**
     * Calculate grade letter based on mark (0-20 scale)
     */
    private function calculateGradeLetter($mark)
    {
        if ($mark >= 16) return 'A'; // Excellent
        if ($mark >= 14) return 'B'; // Very Good
        if ($mark >= 12) return 'C'; // Good
        if ($mark >= 10) return 'D'; // Pass
        return 'F'; // Fail
    }
}
