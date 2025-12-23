<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        // SPECIALTY 1 (DD)
        // Semester 1
        Module::create(['specialty_id' => 1, 'name' => 'Algorithmique', 'code' => 'DD-ALG', 'semester' => 1, 'coefficient' => 2.0, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 1, 'name' => 'Programmation Web', 'code' => 'DD-WEB', 'semester' => 1, 'coefficient' => 2.0, 'hours_per_week' => 5]);
        Module::create(['specialty_id' => 1, 'name' => 'Base de données', 'code' => 'DD-BDD', 'semester' => 1, 'coefficient' => 1.5, 'hours_per_week' => 3]);
        Module::create(['specialty_id' => 1, 'name' => 'Anglais Technique', 'code' => 'DD-ANG1', 'semester' => 1, 'coefficient' => 1.0, 'hours_per_week' => 2]);

        // Semester 2
        Module::create(['specialty_id' => 1, 'name' => 'Java', 'code' => 'DD-JAV', 'semester' => 2, 'coefficient' => 2.0, 'hours_per_week' => 5]);
        Module::create(['specialty_id' => 1, 'name' => 'HTML/CSS', 'code' => 'DD-HTM', 'semester' => 2, 'coefficient' => 1.5, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 1, 'name' => 'JavaScript', 'code' => 'DD-JS', 'semester' => 2, 'coefficient' => 1.5, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 1, 'name' => 'Anglais 2', 'code' => 'DD-ANG2', 'semester' => 2, 'coefficient' => 1.0, 'hours_per_week' => 2]);

        // Semester 3
        Module::create(['specialty_id' => 1, 'name' => 'PHP Laravel', 'code' => 'DD-LAR', 'semester' => 3, 'coefficient' => 2.0, 'hours_per_week' => 5]);
        Module::create(['specialty_id' => 1, 'name' => 'React.js', 'code' => 'DD-REA', 'semester' => 3, 'coefficient' => 2.0, 'hours_per_week' => 5]);
        Module::create(['specialty_id' => 1, 'name' => 'DevOps', 'code' => 'DD-DEV', 'semester' => 3, 'coefficient' => 1.5, 'hours_per_week' => 3]);
        Module::create(['specialty_id' => 1, 'name' => 'Mobile Android', 'code' => 'DD-AND', 'semester' => 3, 'coefficient' => 1.5, 'hours_per_week' => 4]);

        // SPECIALTY 2 (GE)
        // Semester 1
        Module::create(['specialty_id' => 2, 'name' => 'Comptabilité Générale', 'code' => 'GE-CG1', 'semester' => 1, 'coefficient' => 2.0, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 2, 'name' => 'Management', 'code' => 'GE-MNG', 'semester' => 1, 'coefficient' => 2.0, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 2, 'name' => 'Marketing', 'code' => 'GE-MKT', 'semester' => 1, 'coefficient' => 1.5, 'hours_per_week' => 3]);
        Module::create(['specialty_id' => 2, 'name' => 'Anglais', 'code' => 'GE-ANG1', 'semester' => 1, 'coefficient' => 1.0, 'hours_per_week' => 2]);

        // Semester 2
        Module::create(['specialty_id' => 2, 'name' => 'GRH', 'code' => 'GE-GRH', 'semester' => 2, 'coefficient' => 2.0, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 2, 'name' => 'Droit des Affaires', 'code' => 'GE-DRT', 'semester' => 2, 'coefficient' => 1.5, 'hours_per_week' => 3]);
        Module::create(['specialty_id' => 2, 'name' => 'Finance', 'code' => 'GE-FIN', 'semester' => 2, 'coefficient' => 2.0, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 2, 'name' => 'Statistiques', 'code' => 'GE-STA', 'semester' => 2, 'coefficient' => 1.0, 'hours_per_week' => 3]);

        // Semester 3
        Module::create(['specialty_id' => 2, 'name' => 'Contrôle de Gestion', 'code' => 'GE-CTR', 'semester' => 3, 'coefficient' => 2.0, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 2, 'name' => 'Communication', 'code' => 'GE-COM', 'semester' => 3, 'coefficient' => 1.5, 'hours_per_week' => 3]);

        // SPECIALTY 3 (CF)
        // Semester 1
        Module::create(['specialty_id' => 3, 'name' => 'Comptabilité Générale 1', 'code' => 'CF-CG1', 'semester' => 1, 'coefficient' => 2.0, 'hours_per_week' => 5]);
        Module::create(['specialty_id' => 3, 'name' => 'Fiscalité', 'code' => 'CF-FIS', 'semester' => 1, 'coefficient' => 2.0, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 3, 'name' => 'Droit', 'code' => 'CF-DRT', 'semester' => 1, 'coefficient' => 1.5, 'hours_per_week' => 3]);
        Module::create(['specialty_id' => 3, 'name' => 'Anglais', 'code' => 'CF-ANG1', 'semester' => 1, 'coefficient' => 1.0, 'hours_per_week' => 2]);

        // Semester 2
        Module::create(['specialty_id' => 3, 'name' => 'Comptabilité Analytique', 'code' => 'CF-CAN', 'semester' => 2, 'coefficient' => 2.0, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 3, 'name' => 'Audit', 'code' => 'CF-AUD', 'semester' => 2, 'coefficient' => 2.0, 'hours_per_week' => 4]);
        Module::create(['specialty_id' => 3, 'name' => 'Finance d\'Entreprise', 'code' => 'CF-FIN', 'semester' => 2, 'coefficient' => 1.5, 'hours_per_week' => 3]);
        Module::create(['specialty_id' => 3, 'name' => 'Informatique de Gestion', 'code' => 'CF-INF', 'semester' => 2, 'coefficient' => 1.5, 'hours_per_week' => 3]);
    }
}
