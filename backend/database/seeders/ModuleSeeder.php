<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $specialties = Specialty::all();

        $modulesBySpecialty = [
            'EB' => ['Électricité Générale', 'Installation Basse Tension', 'Sécurité Électrique', 'Maintenance Préventive'],
            'EL' => ['Électronique Analogique', 'Électronique Numérique', 'Microcontrôleurs', 'Diagnostic et Réparation'],
            'PS' => ['Installations Sanitaires', 'Tuyauterie', 'Étanchéité', 'Normes Plomberie'],
            'MN' => ['Techniques de Maçonnerie', 'Matériaux Bâtiment', 'Sécurité Chantier', 'Finitions'],
            'MB' => ['Technologie du Bois', 'Menuiserie Intérieure', 'Finitions Bois', 'Ébénisterie'],
            'IT' => ['Programmation PHP', 'Développement Web', 'Base de Données', 'Architecture Logicielle'],
            'GC' => ['Gestion Commerciale', 'Marketing', 'Comptabilité', 'Ressources Humaines'],
        ];

        foreach ($specialties as $specialty) {
            $modules = $modulesBySpecialty[$specialty->code] ?? [];

            foreach ($modules as $index => $moduleName) {
                Module::create([
                    'specialty_id' => $specialty->id,
                    'name' => $moduleName,
                    'code' => strtoupper($specialty->code) . '-M' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                    'description' => "Module de formation : {$moduleName}",
                    'semester' => rand(1, 4),
                    'coefficient' => rand(1, 3),
                    'hours_per_week' => rand(2, 6),
                ]);
            }
        }
    }
}
