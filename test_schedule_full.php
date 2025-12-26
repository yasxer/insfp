<?php
require 'backend/vendor/autoload.php';
$app = require_once 'backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\Schedule;
use Carbon\Carbon;

$student = Student::first();

echo "=== معلومات الطالب ===\n";
echo "Student ID: {$student->id}\n";
echo "Specialty: {$student->specialty_id}\n";
echo "Semester: {$student->current_semester}\n\n";

echo "=== البحث عن الجداول ===\n";
$schedules = Schedule::where('specialty_id', $student->specialty_id)
    ->where('semester', $student->current_semester)
    ->with(['module', 'teacher'])
    ->get();

echo "عدد الجداول: " . count($schedules) . "\n";

if (count($schedules) > 0) {
    echo "\n=== تفاصيل الجداول ===\n";
    foreach ($schedules as $s) {
        echo "اليوم: {$s->day}\n";
        echo "الوقت: {$s->start_time}\n";
        echo "الموديول: {$s->module->name}\n";
        echo "---\n";
    }
} else {
    echo "لا توجد جداول!\n";
}

echo "\n=== محاكاة الـ API Response ===\n";

$dayMap = [
    'monday' => 1,
    'tuesday' => 2,
    'wednesday' => 3,
    'thursday' => 4,
    'friday' => 5,
    'saturday' => 6,
];

$startOfWeek = now()->startOfWeek();
$weekSchedules = collect();

foreach ($schedules as $schedule) {
    $dayOfWeek = $dayMap[strtolower($schedule->day)] ?? null;
    if ($dayOfWeek) {
        $date = $startOfWeek->copy()->setISODate($startOfWeek->year, $startOfWeek->weekOfYear, $dayOfWeek);
        $weekSchedules->push([
            'module' => [
                'id' => $schedule->module->id,
                'name' => $schedule->module->name,
                'code' => $schedule->module->code,
            ],
            'teacher' => [
                'full_name' => $schedule->teacher->full_name ?? 'N/A',
            ],
            'start_time' => $schedule->start_time,
            'room' => $schedule->classroom,
            'date' => $date->format('Y-m-d'),
            'day_name' => $date->locale('fr')->isoFormat('dddd'),
        ]);
    }
}

$schedulesByDay = $weekSchedules->sortBy('start_time')->groupBy('date')->map(function($daySchedules, $date) {
    return [
        'date' => $date,
        'day_name' => $daySchedules->first()['day_name'],
        'classes' => $daySchedules->values()->toArray(),
    ];
})->values()->toArray();

echo json_encode($schedulesByDay, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
