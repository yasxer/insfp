<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        Holiday::create(['name' => 'Vacances d\'hiver', 'start_date' => '2025-01-15', 'end_date' => '2025-01-31', 'description' => 'Vacances de semestre']);
        Holiday::create(['name' => 'Vacances de printemps', 'start_date' => '2025-03-20', 'end_date' => '2025-04-05', 'description' => 'Vacances de printemps']);
        Holiday::create(['name' => 'Aid Al Fitr', 'start_date' => '2025-04-01', 'end_date' => '2025-04-03', 'description' => 'Aid Al Fitr']);
        Holiday::create(['name' => 'Fête du Travail', 'start_date' => '2025-05-01', 'end_date' => '2025-05-01', 'description' => 'Fête du Travail']);
        Holiday::create(['name' => 'Vacances d\'été', 'start_date' => '2025-07-01', 'end_date' => '2025-08-31', 'description' => 'Vacances d\'été']);
    }
}
