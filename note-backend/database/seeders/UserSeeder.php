<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;  // Add this line
use App\Models\User;  // Also make sure you have this

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',     
            'email' => 'admin@example.com',
            'password' => Hash::make('securepassword123'),
            'api_token' => 'demo-auth-token-12345', // Add custom API token
        ]);
    }
}