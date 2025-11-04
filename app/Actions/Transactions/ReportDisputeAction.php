<?php

namespace App\Actions\Transactions;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ReportDisputeAction
{
    /**
     * Marks a transaction as disputed and logs the reason.
     *
     * @param int $transactionId The ID of the transaction.
     * @param string $reason The reason for the dispute.
     */
    public function execute(int $transactionId, string $reason): void
    {
        $transaction = Transaction::findOrFail($transactionId);

        // Safety check
        if (!in_array($transaction->status, ['IN_PROGRESS', 'PENDING_ESCROW'])) {
            throw new \Exception("Only active or pending transactions can be disputed.");
        }

        DB::transaction(function () use ($transaction, $reason) {
            $transaction->update([
                'status' => 'DISPUTED',
                'dispute_reason' => $reason,
            ]);

            // In a real app, you would notify administrators or a dispute resolution team
            // event(new \App\Events\DisputeReported($transaction));
        });
    }
}
