<?php

namespace App\Actions\Transactions;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;

class FinalizeExchangeAction
{
    public function execute(int $transactionId, string $method): void
    {
        $transaction = Transaction::with('angel')->findOrFail($transactionId);
        $angelWallet = $transaction->angel->wallet;
        $amount = $transaction->escrow_amount;
        $assetType = $transaction->escrow_asset;
        $assetColumn = strtolower($assetType) . '_balance';

        DB::transaction(function () use ($transaction, $angelWallet, $amount, $assetType, $assetColumn) {
            // 1. Release Escrowed Funds to the Angel
            // The funds were already deducted from the Receiver in the EscrowFundsAction (Step 4),
            // they are now added to the Angel's coin/gift card balance.
            $angelWallet->increment($assetColumn, $amount);

            // 2. Update Transaction Status
            $transaction->update([
                'status' => 'COMPLETED',
                'payment_method' => $method,
                'escrow_amount' => 0, // Escrow released
            ]);

            // 3. Calculate and Grant Angel Rewards (e.g., 5 points per $100 exchanged)
            $points = max(1, floor($amount / 100) * 5); // Example calculation
            Reward::create([
                'user_id' => $transaction->angel_id,
                'points' => $points,
                'type' => 'EXCHANGE_COMPLETED',
                'description' => "Exchange of {$amount} {$assetType} for cash.",
            ]);

            // 4. Broadcast completion
            event(new \App\Events\TransactionStatusUpdated($transaction));
        });
    }
}
