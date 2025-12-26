<?php
require 'backend/vendor/autoload.php';
$app = require_once 'backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "All students:\n";
$students = DB::table('students')->limit(5)->get();
foreach($students as $s) {
    echo "ID: {$s->id}, User ID: {$s->user_id}, Specialty: {$s->specialty_id}, Semester: {$s->current_semester}\n";
}

echo "\n\nAll users:\n";
$users = DB::table('users')->limit(5)->get();
foreach($users as $u) {
    echo "ID: {$u->id}, Email: {$u->email}, Role: {$u->role}\n";
}

echo "\n\nSchedules for specialty 1, semester 1:\n";
$schedules = DB::table('schedules')
    ->where('specialty_id', 1)
    ->where('semester', 1)
    ->get();
echo "Count: " . count($schedules) . "\n";
foreach($schedules as $s) {
    echo "Day: {$s->day}, Time: {$s->start_time}\n";
}
