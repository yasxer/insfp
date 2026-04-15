<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingSession;
use App\Models\Specialty;
use App\Models\SessionSpecialty;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SessionSpecialtyStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = [
            ['month' => 9, 'year' => 2023, 'is_active' => true],
            ['month' => 2, 'year' => 2024, 'is_active' => true],
            ['month' => 9, 'year' => 2024, 'is_active' => true],
        ];

        $createdSessions = [];
        foreach ($sessions as $sData) {
            $session = TrainingSession::where('month', $sData['month'])
                ->where('year', $sData['year'])
                ->first();

            if (!$session) {
                $session = new TrainingSession();
                $session->month = $sData['month'];
                $session->year = $sData['year'];
                $session->is_active = $sData['is_active'];
                $session->save();
            }
            $createdSessions[] = $session;
        }

        $specialties = [
            Specialty::firstOrCreate(
                ['code' => 'DEV'],
                [
                    'name' => 'Développement Web et Mobile',
                    'study_mode' => 'initial',
                    'duration_semesters' => 4,
                    'duration_years' => 2.5,
                    'current_semester' => 1,
                    'is_active' => true,
                ]
            ),
            Specialty::firstOrCreate(
                ['code' => 'RSD'],
                [
                    'name' => 'Réseaux et Systèmes Informatiques',
                    'study_mode' => 'initial',
                    'duration_semesters' => 4,
                    'duration_years' => 2.5,
                    'current_semester' => 1,
                    'is_active' => true,
                ]
            )
        ];

        $studyTypes = ['presential', 'apprentissage'];

        foreach ($createdSessions as $session) {
            foreach ($specialties as $specialty) {
                foreach ($studyTypes as $studyType) {

                    $sessionSpecialty = SessionSpecialty::firstOrCreate([
                        'session_id' => $session->id,
                        'specialty_id' => $specialty->id,
                        'study_type' => $studyType,
                    ]);

                    $monthsDiff = Carbon::now()->diffInMonths($session->start_date);
                    $currentSemester = floor($monthsDiff / 6) + 1;
                    if ($currentSemester < 1) $currentSemester = 1;
                    if ($currentSemester > 5) $currentSemester = 5;

                    for ($i = 1; $i <= 3; $i++) {
                        $firstName = "Etd" . $i;
                        $lastName = "S" . $session->year . strtolower(substr($studyType, 0, 3)) . "Sp" . $specialty->id;
                        $email = strtolower($firstName . '.' . $lastName . '@insfp.dz');

                        $user = User::firstOrCreate(
                            ['email' => $email],
                            [
                                'password' => Hash::make('password'),
                                'role' => 'student',
                                'is_approved' => true,
                            ]
                        );

                        $studyModeMap = [
                            'presential' => 'initial',
                            'apprentissage' => 'alternance',
                            'cours_soir' => 'continue'
                        ];

                        Student::firstOrCreate(
                            ['user_id' => $user->id],
                            [
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                                'date_of_birth' => Carbon::now()->subYears(rand(18, 25))->subDays(rand(1, 365)),
                                'address' => 'Adresse Exemple ' . $i,
                                'specialty_id' => $specialty->id,
                                'session_specialty_id' => $sessionSpecialty->id,
                                'registration_number' => $session->year . str_pad($specialty->id, 2, '0', STR_PAD_LEFT) . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                                'study_mode' => $studyModeMap[$studyType] ?? 'initial',
                                'current_semester' => $currentSemester,
                                'group' => 'G1',
                                'years_enrolled' => max(1, floor($monthsDiff / 12)),
                            ]
                        );
                    }
                }
            }
        }
    }
}
