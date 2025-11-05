<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;

class ExchangeInitiator extends Component
{
    public $transactionId;
    public $transaction;

    protected $listeners = ['transactionUpdated' => 'loadTransaction'];

    public function mount()
    {
        $this->loadTransaction();
    }

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
     */
    public function confirmReceipt()
    {
        if (!$this->transaction || $this->transaction->status !== 'IN_PROGRESS') {
            session()->flash('error', 'Transaction not found or is not in progress.');
            return;
        }

        try {
            DB::transaction(function () {
                // --- 1. FINALIZATION LOGIC ---
                // a. Update Transaction Status
                $this->transaction->update([
                    'status' => 'COMPLETED',
                    'payment_method' => 'Cash Received Confirmation',
                ]);

                // b. Grant Reward to Angel
                Reward::create([
                    'user_id' => $this->transaction->angel_id,
                    'points' => 10, // Example: 10 points per completion
                    'type' => 'EXCHANGE_COMPLETED',
                    'description' => 'Reward for completing transaction #' . $this->transaction->id,
                ]);

                // c. Angel Fund Transfer/Bonus
                $angelWallet = Wallet::where('user_id', $this->transaction->angel_id)->first();
                if ($angelWallet) {
                    // Assuming a small bonus is paid to the Angel upon completion
                    $angelWallet->increment('coin_balance', 0.50);
                }

                session()->flash('success', 'Receipt confirmed! Funds released and Angel rewarded.');
                $this->dispatch('transactionUpdated'); // Refresh data
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Finalization failed: ' . $e->getMessage());
        }
    }

    /**
     * Dispatches the event to open the dispute form with the transaction ID.
     * This is the fix for the dispatch dependency error.
     */
    public function dispatchDisputeForm()
    {
        if ($this->transactionId) {
            $this->dispatch('openDisputeForm', transactionId: $this->transactionId);
        }
    }

    public function render()
    {
        return view('livewire.exchange-initiator');
    }
}