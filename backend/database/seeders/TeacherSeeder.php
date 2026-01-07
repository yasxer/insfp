<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teacherUsers = User::where('role', 'teacher')->get();

        $firstNames = ['Ahmed', 'Fatima', 'Mohamed', 'Aisha', 'Hassan', 'Leila', 'Ibrahim', 'Zahra', 'Riad', 'Safiya'];
        $lastNames = ['Hassan', 'Bennani', 'Alami', 'Boubekri', 'Cherkaoui', 'Kabbaj', 'Lahcen', 'Tazi', 'Benali', 'Srhir'];
        $specializations = ['Électricité', 'Électronique', 'Plomberie', 'Maçonnerie', 'Menuiserie', 'Informatique', 'Gestion'];

        foreach ($teacherUsers as $index => $user) {
            Teacher::create([
                'user_id' => $user->id,
                'first_name' => $firstNames[$index % count($firstNames)],
                'last_name' => $lastNames[$index % count($lastNames)],
                'specialization' => $specializations[array_rand($specializations)],
            ]);
        }
    }
}
