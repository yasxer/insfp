<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Test with user ID 3 (ysxr@gmail.com)
$user = App\Models\User::find(3);

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "Testing Profile API for User:\n";
echo "Email: {$user->email}\n";
echo "Role: {$user->role}\n\n";

$student = $user->student()->with('specialty')->first();

if (!$student) {
    echo "No student profile found for this user!\n";
    exit;
}

echo "Profile Data (as API would return):\n";
echo json_encode([
    'id' => $student->id,
    'registration_number' => $student->registration_number,
    'first_name' => $student->first_name,
    'last_name' => $student->last_name,
    'date_of_birth' => $student->date_of_birth,
    'address' => $student->address,
    'email' => $user->email,
    'phone' => $user->phone,
    'specialty' => [
        'id' => $student->specialty->id,
        'name' => $student->specialty->name,
        'code' => $student->specialty->code,
    ],
    'current_semester' => $student->current_semester,
    'study_mode' => $student->study_mode,
    'years_enrolled' => $student->years_enrolled,
    'is_graduated' => $student->is_graduated,
    'created_at' => $student->created_at->format('Y-m-d'),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
