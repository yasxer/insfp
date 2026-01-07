<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin users
        User::create([
            'email' => 'admin@insfp.local',
            'phone' => '0612345670',
            'password' => Hash::make('password123'),
            'role' => 'administration',
            'is_approved' => true,
        ]);

        // Create directors
        for ($i = 1; $i <= 2; $i++) {
            User::create([
                'email' => "director{$i}@insfp.local",
                'phone' => '06' . rand(10000000, 99999999),
                'password' => Hash::make('password123'),
                'role' => 'administration',
                'is_approved' => true,
            ]);
        }

        // Create teachers
        $firstNames = ['Ahmed', 'Fatima', 'Mohamed', 'Aisha', 'Hassan', 'Leila', 'Ibrahim', 'Zahra'];
        $lastNames = ['Hassan', 'Bennani', 'Alami', 'Boubekri', 'Cherkaoui', 'Kabbaj', 'Lahcen', 'Tazi'];

        for ($i = 1; $i <= 15; $i++) {
            User::create([
                'email' => "teacher{$i}@insfp.local",
                'phone' => '06' . rand(10000000, 99999999),
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'is_approved' => true,
            ]);
        }

        // Create students
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'email' => "student{$i}@insfp.local",
                'phone' => '06' . rand(10000000, 99999999),
                'password' => Hash::make('password123'),
                'role' => 'student',
                'is_approved' => true,
            ]);
        }
    }
}
