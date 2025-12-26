<?php
require 'backend/vendor/autoload.php';
$app = require_once 'backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\Schedule;
use Carbon\Carbon;

// Get first student  
$student = Student::first();
echo "Testing with Student ID: {$student->id}, User ID: {$student->user_id}, Specialty: {$student->specialty_id}, Semester: {$student->current_semester}\n\n";

// Get schedules like the API does
$schedules = Schedule::where('specialty_id', $student->specialty_id)
    ->where('semester', $student->current_semester)
    ->with(['module', 'teacher'])
    ->get();

echo "Found " . count($schedules) . " schedules\n\n";

$weekSchedules = collect();
$dayMap = [
    'monday' => 1,
    'tuesday' => 2,
    'wednesday' => 3,
    'thursday' => 4,
    'friday' => 5,
    'saturday' => 6,
    'sunday' => 7,
];

$startOfWeek = now()->startOfWeek();
$endOfWeek = $startOfWeek->copy()->endOfWeek();

echo "Week: {$startOfWeek->format('Y-m-d')} to {$endOfWeek->format('Y-m-d')}\n";
echo "Start of week ISO: " . $startOfWeek->weekOfYear . "\n\n";

foreach ($schedules as $schedule) {
    $dayOfWeek = $dayMap[strtolower($schedule->day)] ?? null;
    if ($dayOfWeek) {
        $date = $startOfWeek->copy()->setISODate($startOfWeek->year, $startOfWeek->weekOfYear, $dayOfWeek);
        $dayName = $date->locale('fr')->isoFormat('dddd');
        echo "Schedule: Day={$schedule->day}, Module={$schedule->module->name}, Time={$schedule->start_time}\n";
        echo "  -> Mapped to: Date={$date->format('Y-m-d')}, Day Name={$dayName}\n";
    }
}
