<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        Specialty::create([
            'name' => 'Développement Digital',
            'code' => 'DD01',
            'description' => 'Formation en développement web et mobile',
            'duration_semesters' => 6,
            'is_active' => true,
        ]);

        Specialty::create([
            'name' => "Gestion d'Entreprise",
            'code' => 'GE01',
            'description' => 'Formation en management et gestion',
            'duration_semesters' => 6,
            'is_active' => true,
        ]);

        Specialty::create([
            'name' => 'Comptabilité et Fiscalité',
            'code' => 'CF01',
            'description' => 'Formation en comptabilité',
            'duration_semesters' => 6,
            'is_active' => true,
        ]);
    }
}
