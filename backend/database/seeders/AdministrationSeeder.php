<?php

namespace Database\Seeders;

use App\Models\Administration;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdministrationSeeder extends Seeder
{
    public function run(): void
    {
        $administrationUsers = User::where('role', 'administration')->get();

        $firstNames = ['Ahmed', 'Fatima', 'Mohamed', 'Aisha', 'Hassan'];
        $lastNames = ['Hassan', 'Bennani', 'Alami', 'Boubekri', 'Cherkaoui'];
        $positions = ['Director', 'Vice Director', 'Secretary', 'Financial Manager'];

        foreach ($administrationUsers as $index => $user) {
            Administration::create([
                'user_id' => $user->id,
                'first_name' => $firstNames[$index % count($firstNames)],
                'last_name' => $lastNames[$index % count($lastNames)],
                'position' => $positions[$index % count($positions)],
            ]);
        }
    }
}
