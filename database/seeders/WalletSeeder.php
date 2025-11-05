<?php

namespace Database\Seeders;

use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Wallet for John Receiver (ID 1) - Needs funds to escrow
        Wallet::create([
            'user_id' => 1,
            'coin_balance' => 250.00,
            'gift_card_balance' => 50.00,
        ]);

        // Wallet for Angel Michael (ID 2) - Needs a wallet to receive bonuses
        Wallet::create([
            'user_id' => 2,
            'coin_balance' => 100.00,
            'gift_card_balance' => 0.00,
        ]);

        // Ensure all users have a wallet
        \App\Models\User::all()->each(function ($user) {
            if (!$user->wallet) {
                Wallet::create([
                    'user_id' => $user->id,
                    'coin_balance' => 10.00,
                    'gift_card_balance' => 0.00,
                ]);
            }
        });
    }
}