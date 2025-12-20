<?php

namespace Database\Seeders;

use App\Models\Administration;
use Illuminate\Database\Seeder;

class AdministrationSeeder extends Seeder
{
    public function run(): void
    {
        Administration::create([
            'user_id' => 1,
            'first_name' => 'Mohammed',
            'last_name' => 'Alami',
            'position' => 'Directeur des Études',
        ]);
    }
}
