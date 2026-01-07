<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            [
                'name' => 'Électricité Bâtiment',
                'code' => 'EB',
                'description' => 'Formation en électricité du bâtiment et installations électriques',
                'duration_semesters' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Électronique',
                'code' => 'EL',
                'description' => 'Formation en électronique et maintenance électronique',
                'duration_semesters' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Plomberie Sanitaire',
                'code' => 'PS',
                'description' => 'Formation en plomberie sanitaire et installations',
                'duration_semesters' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Maçonnerie',
                'code' => 'MN',
                'description' => 'Formation en maçonnerie et construction',
                'duration_semesters' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Menuiserie Bois',
                'code' => 'MB',
                'description' => 'Formation en menuiserie et travail du bois',
                'duration_semesters' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Informatique',
                'code' => 'IT',
                'description' => 'Formation en informatique et développement',
                'duration_semesters' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Gestion Commerciale',
                'code' => 'GC',
                'description' => 'Formation en gestion et commerce',
                'duration_semesters' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($specialties as $specialty) {
            Specialty::create($specialty);
        }
    }
}
