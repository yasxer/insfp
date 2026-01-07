<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $studentUsers = User::where('role', 'student')->get();
        $specialties = Specialty::all();

        $firstNames = ['Ahmed', 'Fatima', 'Mohamed', 'Aisha', 'Hassan', 'Leila', 'Ibrahim', 'Zahra', 'Riad', 'Safiya'];
        $lastNames = ['Hassan', 'Bennani', 'Alami', 'Boubekri', 'Cherkaoui', 'Kabbaj', 'Lahcen', 'Tazi', 'Benali', 'Srhir'];
        $studyModes = ['initial', 'alternance', 'continue'];

        $studentIndex = 0;
        foreach ($studentUsers as $user) {
            $specialty = $specialties->random();

            Student::create([
                'user_id' => $user->id,
                'specialty_id' => $specialty->id,
                'registration_number' => 'STU-' . str_pad($studentIndex + 1, 6, '0', STR_PAD_LEFT),
                'first_name' => $firstNames[$studentIndex % count($firstNames)],
                'last_name' => $lastNames[$studentIndex % count($lastNames)],
                'study_mode' => $studyModes[array_rand($studyModes)],
                'current_semester' => rand(1, 4),
                'years_enrolled' => rand(1, 3),
                'is_graduated' => false,
            ]);

            $studentIndex++;
        }
    }
}
