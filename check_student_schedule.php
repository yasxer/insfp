<?php
require 'backend/vendor/autoload.php';
$app = require_once 'backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$student = DB::table('students')->where('user_id', 1)->first();
echo "Student Specialty: {$student->specialty_id}, Semester: {$student->current_semester}\n";

$schedules = DB::table('schedules')
    ->where('specialty_id', $student->specialty_id)
    ->where('semester', $student->current_semester)
    ->get();

echo "Total schedules for student: " . count($schedules) . "\n";
foreach($schedules as $s) {
    echo "Day: {$s->day}, Time: {$s->start_time}, Module: {$s->module_id}\n";
}
