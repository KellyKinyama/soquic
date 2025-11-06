<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FundPurchase extends Component
{
    // ... (rest of the properties remain the same)
    public $assetType = 'Coin';
    public $purchaseAmount;
    public $cardDetails = '**** **** **** 1234';
    public $currentCoinBalance = 0.00;
    public $currentGiftCardBalance = 0.00;

    protected $rules = [
        'assetType' => 'required|in:Coin,Gift_Card',
        'purchaseAmount' => 'required|numeric|min:1|max:10000',
    ];

    public function mount()
    {
        $this->refreshWallet();
    }

    public function refreshWallet()
    {
        $user = Auth::user();
        if ($user && $wallet = $user->wallet) {
            $this->currentCoinBalance = $wallet->coin_balance;
            $this->currentGiftCardBalance = $wallet->gift_card_balance;
        }
    }

    /**
     * Executes the purchase, using DB transaction for safety.
     */
    public function executePurchase()
    {
        $this->validate();

        $user = Auth::user();
        if (!$user) {
            session()->flash('error', 'Authentication Error: Please log in to complete purchase.');
            return;
        }

        // --- CRITICAL CHECK: Ensure Wallet Exists ---
        $wallet = $user->wallet;
        if (!$wallet) {
             // Fallback: Manually create the wallet if it doesn't exist.
             $wallet = Wallet::create(['user_id' => $user->id]);
        }
        // ---------------------------------------------------------------------

        try {
            DB::transaction(function () use ($user, $wallet) {

                // 1. Determine the asset column
                $assetColumn = $this->assetType === 'Coin' ? 'coin_balance' : 'gift_card_balance';

                // 2. Update the Wallet Balance
                $wallet->increment($assetColumn, $this->purchaseAmount);

                // 3. Record the Transaction
                // FIX: Use $user->id for angel_id to satisfy the NOT NULL constraint.
                Transaction::create([
                    'receiver_id' => $user->id,
                    'angel_id' => $user->id, // <<-- FIX APPLIED HERE
                    'escrow_amount' => $this->purchaseAmount,
                    'escrow_asset' => $this->assetType,
                    'status' => 'COMPLETED',
                    'payment_method' => 'BANK_CARD_PURCHASE',
                    'dispute_reason' => 'Initial Funding via Bank Card',
                ]);

                // 4. Success
                $this->refreshWallet();
                $this->reset(['purchaseAmount']);
                session()->flash('success', "Successfully purchased {$this->purchaseAmount} {$this->assetType}s!");
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Purchase failed due to a system error. Please try again or contact support.');
            // Note: If this still fails, you must re-examine your server logs for the exact database error.
            dd($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.fund-purchase');
    }
}