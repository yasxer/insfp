<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update students with sample data
App\Models\Student::where('id', 1)->update([
    'date_of_birth' => '2000-05-15',
    'address' => '123 Rue Ahmed Ben Bella, Alger'
]);

App\Models\Student::where('id', 2)->update([
    'date_of_birth' => '2001-03-20',
    'address' => '456 Avenue de l\'Indépendance, Oran'
]);

App\Models\Student::where('id', 3)->update([
    'date_of_birth' => '2000-08-10',
    'address' => '789 Boulevard Mohamed V, Constantine'
]);

echo "✓ Students updated successfully with sample data!\n";

// Show updated data
$students = App\Models\Student::all();
foreach ($students as $student) {
    echo "\n{$student->first_name} {$student->last_name}:\n";
    echo "  DOB: {$student->date_of_birth}\n";
    echo "  Address: {$student->address}\n";
}
