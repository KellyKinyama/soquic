<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class AngelSelection extends Component
{
    public $angels;
    public $assetType = 'Coin';
    public $exchangeAmount;
    public $selectedAngelId;
    public $transactionId;

    public function mount()
    {
        // Fetch users who are verified Angels
        $this->angels = User::where('is_angel', true)->get();
        // Set a default angel if available
        if ($this->angels->isNotEmpty()) {
            $this->selectedAngelId = $this->angels->first()->id;
        }
    }

    protected $rules = [
        'assetType' => 'required|in:Coin,Gift_Card',
        'exchangeAmount' => 'required|numeric|min:1',
        'selectedAngelId' => 'required|exists:users,id',
    ];

    /**
     * Initiates the transaction and moves funds to escrow in one step.
     */
    public function initiateExchange()
    {
        $this->validate();

        // NOTE: In a real app, use auth()->id() to get the currently logged-in user.
        // Assuming the current user is ID 1 for demonstration
        $receiverId = 1;
        $receiverWallet = Wallet::where('user_id', $receiverId)->first();

        if (!$receiverWallet) {
            session()->flash('error', 'Receiver wallet not found. Please create a wallet first.');
            return;
        }

        try {
            DB::transaction(function () use ($receiverId, $receiverWallet) {
                // --- 1. INITIATE TRANSACTION ---
                $transaction = Transaction::create([
                    'receiver_id' => $receiverId,
                    'angel_id' => $this->selectedAngelId,
                    'escrow_amount' => $this->exchangeAmount,
                    'escrow_asset' => $this->assetType,
                    'status' => 'PENDING_ESCROW',
                ]);

                // --- 2. ESCROW FUNDS (Move from Wallet to Escrow) ---
                $assetColumn = $this->assetType === 'Coin' ? 'coin_balance' : 'gift_card_balance';

                if ($receiverWallet->$assetColumn < $this->exchangeAmount) {
                    throw new \Exception("Insufficient {$this->assetType} balance to cover the exchange amount.");
                }

                // Deduct funds from the receiver's wallet
                $receiverWallet->decrement($assetColumn, $this->exchangeAmount);

                // Update transaction status to IN_PROGRESS after successful escrow
                $transaction->update(['status' => 'IN_PROGRESS']);

                $this->transactionId = $transaction->id;
                session()->flash('success', 'Exchange initiated and funds placed in escrow successfully!');
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Exchange failed: ' . $e->getMessage());
            $this->transactionId = null;
        }
    }

    public function render()
    {
        return view('livewire.angel-selection');
    }
}
