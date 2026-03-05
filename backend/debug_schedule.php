<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$session = \App\Models\TrainingSession::first();
echo 'Session: ' . $session->name . ' (id=' . $session->id . ')' . PHP_EOL;

$sessionSpecialtyIds = \App\Models\SessionSpecialty::where('session_id', $session->id)->pluck('specialty_id');
echo 'Session specialty IDs: ' . $sessionSpecialtyIds->implode(', ') . PHP_EOL;

$count = \App\Models\Student::where('is_graduated', false)
    ->whereIn('specialty_id', $sessionSpecialtyIds)
    ->whereNotNull('current_semester')
    ->count();
echo 'Students matching (new query): ' . $count . PHP_EOL;

$combos = \App\Models\Student::select(
        'specialty_id', 'study_mode', 'current_semester',
        \Illuminate\Support\Facades\DB::raw('COUNT(*) as students_count')
    )
    ->where('is_graduated', false)
    ->whereIn('specialty_id', $sessionSpecialtyIds)
    ->whereNotNull('specialty_id')->whereNotNull('current_semester')
    ->groupBy('specialty_id', 'study_mode', 'current_semester')
    ->get();

echo 'Combinations found: ' . $combos->count() . PHP_EOL;
foreach ($combos as $c) {
    echo " - specialty_id={$c->specialty_id} study_mode={$c->study_mode} semester={$c->current_semester} count={$c->students_count}" . PHP_EOL;
}
