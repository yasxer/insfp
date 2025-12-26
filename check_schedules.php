<?php
require 'backend/vendor/autoload.php';
$app = require_once 'backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$count = DB::table('schedules')->count();
echo "Total schedules: $count\n\n";

$schedules = DB::table('schedules')->limit(5)->get();
foreach ($schedules as $schedule) {
    echo "ID: {$schedule->id}, Day: {$schedule->day}, Time: {$schedule->start_time}, Semester: {$schedule->semester}, Specialty: {$schedule->specialty_id}\n";
}

echo "\n\nSchedules for current student's specialty:\n";
$student = DB::table('students')->where('user_id', 1)->first();
if ($student) {
    echo "Student specialty: {$student->specialty_id}, Semester: {$student->current_semester}\n";
    $schedules = DB::table('schedules')
        ->where('specialty_id', $student->specialty_id)
        ->where('semester', $student->current_semester)
        ->get();
    echo "Schedules for this specialty/semester: " . count($schedules) . "\n";
    foreach ($schedules as $s) {
        echo "  - Day: {$s->day}, Time: {$s->start_time}, Module: {$s->module_id}\n";
    }
}
