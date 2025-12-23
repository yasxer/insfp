<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        Teacher::create([
            'user_id' => 2,
            'first_name' => 'Ahmed',
            'last_name' => 'Bennani',
            'specialization' => 'Informatique',
        ]);

        Teacher::create([
            'user_id' => 3,
            'first_name' => 'Fatima',
            'last_name' => 'Zahra',
            'specialization' => 'Management',
        ]);

        Teacher::create([
            'user_id' => 4,
            'first_name' => 'Youssef',
            'last_name' => 'Amrani',
            'specialization' => 'Mathématiques',
        ]);

        Teacher::create([
            'user_id' => 5,
            'first_name' => 'Khadija',
            'last_name' => 'El Fassi',
            'specialization' => 'Comptabilité',
        ]);

        Teacher::create([
            'user_id' => 6,
            'first_name' => 'Omar',
            'last_name' => 'Idrissi',
            'specialization' => 'Anglais',
        ]);
    }
}
