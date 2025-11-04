<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;

class ExchangeInitiator extends Component
{
    // Public properties mapped to the view
    public $transactionId;
    public $transaction;

    // Listeners for component communication (e.g., from the Dispute Form)
    protected $listeners = ['transactionUpdated' => 'loadTransaction'];

    public function mount()
    {
        // Load transaction if an ID is set on the URL/initial component state
        $this->loadTransaction();
    }

    /**
     * Loads the transaction details from the database.
     */
    public function loadTransaction()
    {
        if ($this->transactionId) {
            // Eager load the Angel for display purposes
            $this->transaction = Transaction::with('angel')->find($this->transactionId);
        } else {
            $this->transaction = null;
        }
    }

    /**
     * Confirms receipt of cash payment, finalizes the transaction, and rewards the Angel.
     * This method contains the core FINALIZATION business logic.
     */
    public function confirmReceipt()
    {
        // Ensure data is fresh before acting
        $this->loadTransaction();

        if (!$this->transaction || $this->transaction->status !== 'IN_PROGRESS') {
            session()->flash('error', 'Transaction not found or is not in progress.');
            return;
        }

        try {
            DB::transaction(function () {
                // --- 1. Update Transaction Status ---
                $this->transaction->update([
                    'status' => 'COMPLETED',
                    'payment_method' => 'Cash Received Confirmation',
                ]);

                // --- 2. Grant Reward to Angel ---
                // Rewards the Angel for completing the exchange
                Reward::create([
                    'user_id' => $this->transaction->angel_id,
                    'points' => 10, // Example: 10 points per completion
                    'type' => 'EXCHANGE_COMPLETED',
                    'description' => 'Reward for completing transaction #' . $this->transaction->id,
                ]);

                // --- 3. Angel Fund Transfer/Bonus ---
                // Adds a small operational bonus to the Angel's wallet (optional business rule)
                $angelWallet = Wallet::where('user_id', $this->transaction->angel_id)->first();
                if ($angelWallet) {
                    $angelWallet->increment('coin_balance', 0.50);
                }

                session()->flash('success', 'Receipt confirmed! Funds released and Angel rewarded.');
                // Trigger view refresh
                $this->dispatch('transactionUpdated');
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Finalization failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.exchange-initiator');
    }
}
