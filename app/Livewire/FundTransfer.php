<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FundTransfer extends Component
{
    // State properties for the form
    public $recipientEmail = '';
    public $assetType = 'Coin';
    public $amount;

    // Display properties
    public $currentCoinBalance = 0.00;
    public $currentGiftCardBalance = 0.00;
    public $successMessage = '';

    // Mock initial balance for the current user for demonstration
    public $senderId;

    protected $rules = [
        'recipientEmail' => 'required|email|exists:users,email',
        'assetType' => 'required|in:Coin,Gift_Card',
        'amount' => 'required|numeric|min:1',
    ];

    protected $messages = [
        'recipientEmail.exists' => 'The recipient email must belong to a registered user.',
    ];

    public function mount()
    {
        // NOTE: In a real app, use Auth::id()
        $this->senderId = Auth::id() ?? 1; // Assuming user ID 1 is the sender for demo

        $this->loadSenderWallet();
    }

    /**
     * Loads and updates the sender's current wallet balances.
     */
    public function loadSenderWallet()
    {
        // Use the authenticated user's ID
        $user = Auth::user();

        // Use the wallet relationship defined in your User model
        if ($user && $wallet = $user->wallet) {
            $this->currentCoinBalance = $wallet->coin_balance;
            $this->currentGiftCardBalance = $wallet->gift_card_balance;
        } else {
            // Failsafe: should not happen if User::boot() is working
            $this->currentCoinBalance = 0.00;
            $this->currentGiftCardBalance = 0.00;
        }
    }

    /**
     * Executes the P2P fund transfer.
     */
    public function submitTransfer()
    {
        $this->validate();

        $assetColumn = $this->assetType === 'Coin' ? 'coin_balance' : 'gift_card_balance';
        $currentBalance = $this->assetType === 'Coin' ? $this->currentCoinBalance : $this->currentGiftCardBalance;

        // Custom validation check against the available balance
        if ($this->amount > $currentBalance) {
            $this->addError('amount', "Transfer amount cannot exceed your current {$this->assetType} balance of " . number_format($currentBalance, 2));
            return;
        }

        try {
            DB::transaction(function () use ($assetColumn) {
                // 1. Find Sender and Recipient
                $senderWallet = Auth::user()->wallet; // Guaranteed to exist by User::boot()
                $recipient = User::where('email', $this->recipientEmail)->firstOrFail();
                $recipientWallet = $recipient->wallet; // Guaranteed to exist by User::boot()

                // 2. DEBIT: Deduct from sender's wallet
                $senderWallet->decrement($assetColumn, $this->amount);

                // 3. CREDIT: Add to recipient's wallet
                $recipientWallet->increment($assetColumn, $this->amount);

                // 4. Record the P2P Transaction
                Transaction::create([
                    'receiver_id' => $recipient->id, // The one receiving the asset
                    'angel_id' => Auth::id(), // Re-purposed to be the sender's ID
                    'escrow_amount' => $this->amount,
                    'escrow_asset' => $this->assetType,
                    'status' => 'COMPLETED',
                    'payment_method' => 'P2P_TRANSFER',
                    'dispute_reason' => 'Instant P2P Transfer',
                ]);

                // 5. Success
                $this->loadSenderWallet(); // Refresh balance display
                $this->successMessage = "Success! {$this->amount} {$this->assetType} transferred to {$this->recipientEmail}.";
                $this->reset(['recipientEmail', 'amount']);

                // --- FIX: Use correct Livewire 3 dispatch syntax ---
                $this->dispatch('clear-success-message');
                // --------------------------------------------------
            });

        } catch (\Exception $e) {
            session()->flash('error', 'Transfer failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.fund-transfer');
    }
}