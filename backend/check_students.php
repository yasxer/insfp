<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$students = App\Models\Student::with(['user', 'specialty'])->get();

foreach ($students as $student) {
    echo "========================================\n";
    echo "ID: {$student->id}\n";
    echo "Registration Number: {$student->registration_number}\n";
    echo "First Name: {$student->first_name}\n";
    echo "Last Name: {$student->last_name}\n";
    echo "Date of Birth: " . ($student->date_of_birth ?? 'NULL') . "\n";
    echo "Address: " . ($student->address ?? 'NULL') . "\n";
    echo "Email: {$student->user->email}\n";
    echo "Phone: " . ($student->user->phone ?? 'NULL') . "\n";
    echo "Specialty: {$student->specialty->name} ({$student->specialty->code})\n";
    echo "Current Semester: {$student->current_semester}\n";
    echo "Study Mode: {$student->study_mode}\n";
    echo "========================================\n\n";
}
