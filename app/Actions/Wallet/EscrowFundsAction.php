<?php

namespace App\Actions\Wallet;

use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class EscrowFundsAction
{
    public function execute(Wallet $wallet, Transaction $transaction, float $amount, string $assetType): bool
    {
        // Use a database transaction for financial integrity (ACID)
        return DB::transaction(function () use ($wallet, $transaction, $amount, $assetType) {
            $column = strtolower($assetType) . '_balance';

            // 1. Check for sufficient funds (should be handled earlier, but double-check)
            if ($wallet->$column < $amount) {
                return false;
            }

            // 2. Deduct from the user's available balance
            $wallet->decrement($column, $amount);

            // 3. Mark the transaction itself as holding the escrowed funds (simplified approach)
            // In a real system, you might move this to a dedicated `Escrow` table.
            $transaction->update([
                'escrow_amount' => $amount,
                'escrow_asset' => $assetType,
                'status' => 'MATCHED',
            ]);

            return true;
        });
    }
}
