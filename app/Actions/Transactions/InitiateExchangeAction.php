<?php

namespace App\Actions\Transactions;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class InitiateExchangeAction
{
    /**
     * Creates a new transaction and sets the initial state to PENDING_ANGEL.
     *
     * @param int $receiverId The ID of the user initiating the exchange.
     * @param int $angelId The ID of the Angel chosen for the exchange.
     * @param float $amount The amount to be exchanged (e.g., in USD value).
     * @param string $asset The type of asset (e.g., 'Coin' or 'Gift_Card').
     * @return Transaction
     */
    public function execute(int $receiverId, int $angelId, float $amount, string $asset): Transaction
    {
        // Simple validation check (should be done in a FormRequest or Livewire validation)
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Exchange amount must be greater than zero.");
        }

        return DB::transaction(function () use ($receiverId, $angelId, $amount, $asset) {
            $transaction = Transaction::create([
                'receiver_id' => $receiverId,
                'angel_id' => $angelId,
                'status' => 'PENDING_ESCROW', // Ready for the next step (EscrowFundsAction)
                'escrow_amount' => $amount,
                'escrow_asset' => $asset,
            ]);

            // In a real app, you would dispatch an event here, e.g.,
            // event(new \App\Events\TransactionInitiated($transaction));

            return $transaction;
        });
    }
}
