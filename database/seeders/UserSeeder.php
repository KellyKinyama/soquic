<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Receiver User (ID 1) - The one initiating the exchange
        User::create([
            'id' => 1,
            'name' => 'John Receiver',
            'email' => 'receiver@example.com',
            'password' => Hash::make('password'),
            'is_angel' => false,
        ]);

        // 2. Angel User (ID 2) - The one fulfilling the exchange
        User::create([
            'id' => 2,
            'name' => 'Angel Michael',
            'email' => 'angel@example.com',
            'password' => Hash::make('password'),
            'is_angel' => true,
        ]);

        // Create a few more test users for AngelSelection dropdown
        User::factory(5)->create(['is_angel' => true]);
        User::factory(5)->create(['is_angel' => false]);
    }
}