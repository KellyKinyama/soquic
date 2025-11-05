<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an IN_PROGRESS transaction for immediate testing
        Transaction::create([
            'id' => 101, // Use a non-standard ID to make it obvious this is the test record
            'receiver_id' => 1,
            'angel_id' => 2,
            'escrow_amount' => 75.50,
            'escrow_asset' => 'Coin',
            'status' => 'IN_PROGRESS', // This is the state where the Receiver needs to act
        ]);

        // Create a completed transaction for status check
        Transaction::create([
            'id' => 102,
            'receiver_id' => 1,
            'angel_id' => 2,
            'escrow_amount' => 25.00,
            'escrow_asset' => 'Gift_Card',
            'status' => 'COMPLETED',
            'payment_method' => 'Cash Received Confirmation',
        ]);
    }
}