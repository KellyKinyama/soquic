<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Must be run first as other tables reference users
            UserSeeder::class,
            // Wallets need users to exist
            WalletSeeder::class,
            // Transactions need users to exist
            TransactionSeeder::class,
            RewardSeeder::class,
        ]);
    }
}