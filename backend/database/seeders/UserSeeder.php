<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin user
        User::create([
            'email' => 'admin@insfp.ma',
            'phone' => '0612000001',
            'password' => Hash::make('password'),
            'role' => 'administration',
            'is_approved' => true,
        ]);

        // 2. 5 Teacher users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'email' => "teacher{$i}@insfp.ma",
                'phone' => '061200001' . $i,
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'is_approved' => true,
            ]);
        }

        // 3. 20 Student users
        for ($i = 1; $i <= 20; $i++) {
            $phoneSuffix = str_pad($i, 2, '0', STR_PAD_LEFT);
            User::create([
                'email' => "student{$i}@insfp.ma",
                'phone' => "06120001{$phoneSuffix}",
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_approved' => true,
            ]);
        }
    }
}
