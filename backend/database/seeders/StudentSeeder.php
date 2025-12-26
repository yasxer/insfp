<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = ['Hassan', 'Amina', 'Karim', 'Salma', 'Mehdi', 'Zineb', 'Rachid', 'Nadia', 'Hamza', 'Imane'];
        $lastNames = ['El Amrani', 'Benjelloun', 'Alaoui', 'Tazi', 'Fassi', 'Idrissi', 'Berrada', 'Kettani', 'Squalli', 'Lahlou'];

        for ($i = 1; $i <= 20; $i++) {
            $userId = $i + 6; // Users 7-26

            // Determine specialty
            if ($i <= 8) {
                $specialtyId = 1; // DD
                $codePrefix = 'DD';
            } elseif ($i <= 14) {
                $specialtyId = 2; // GE
                $codePrefix = 'GE';
            } else {
                $specialtyId = 3; // CF
                $codePrefix = 'CF';
            }

            $registrationNumber = $codePrefix . '2024' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $firstName = $firstNames[($i - 1) % count($firstNames)];
            $lastName = $lastNames[($i - 1) % count($lastNames)];

            // Study mode: 70% initial, 30% alternance
            $studyMode = ($i % 10 < 7) ? 'initial' : 'alternance';

            // Set all students to semester 1 for testing
            $currentSemester = 1;
            $yearsEnrolled = 1;

            Student::create([
                'user_id' => $userId,
                'specialty_id' => $specialtyId,
                'registration_number' => $registrationNumber,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'study_mode' => $studyMode,
                'current_semester' => $currentSemester,
                'years_enrolled' => $yearsEnrolled,
                'is_graduated' => false,
            ]);
        }
    }
}
