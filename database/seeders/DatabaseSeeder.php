<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Free Demo Account (100 MB Limit)
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Free Account User',
                'password' => Hash::make('password123'),
                'is_premium' => false,
                'storage_limit' => 104857600, // 100 MB
            ]
        );

        // Premium Demo Account (5 GB Limit)
        User::firstOrCreate(
            ['email' => 'premium@example.com'],
            [
                'name' => 'Premium Account User',
                'password' => Hash::make('password123'),
                'is_premium' => true,
                'storage_limit' => 5368709120, // 5 GB
            ]
        );
    }
}
