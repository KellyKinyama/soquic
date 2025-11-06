<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reward;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RewardsWithdrawal extends Component
{
    // --- Configuration ---
    // 100 points equals 1.00 USD cash value (0.01 USD per point)
    const POINTS_TO_CASH_RATE = 0.01;

    // --- State Properties ---
    public $totalPoints = 0;
    public $convertableCashValue = 0.00;
    public $walletCoinBalance = 0.00;
    public $withdrawalAmount;
    public $bankAccountNumber = '**** 1234'; // Mocked from ProfileSettings scope

    protected $rules = [
        'withdrawalAmount' => 'required|numeric|min:10',
    ];

    public function mount()
    {
        $this->loadData();
    }

    /**
     * Fetches all user data: rewards, wallet balance, and calculates dividend value.
     */
    public function loadData()
    {
        $user = Auth::user();
        if (!$user) return;

        // 1. Calculate Total Available Points
        $this->totalPoints = Reward::where('user_id', $user->id)->sum('points');

        // 2. Calculate Convertable Cash Value
        $this->convertableCashValue = $this->totalPoints * self::POINTS_TO_CASH_RATE;

        // 3. Load Wallet Balance (using coin_balance as the cash-equivalent/redeemable balance)
        $wallet = $user->wallet;
        $this->walletCoinBalance = $wallet ? $wallet->coin_balance : 0.00;

        // Reset form on refresh
        $this->reset(['withdrawalAmount']);
    }

    /**
     * Converts all available points to cash value and credits the wallet.
     */
    public function convertPointsToCash()
    {
        $user = Auth::user();
        if (!$user) return;

        if ($this->totalPoints <= 0) {
            session()->flash('error', 'You have no points to convert into cash dividends.');
            return;
        }

        $cashToCredit = $this->convertableCashValue;
        $pointsToDebit = $this->totalPoints;

        try {
            DB::transaction(function () use ($user, $cashToCredit, $pointsToDebit) {
                $wallet = $user->wallet;

                // 1. CREDIT WALLET: Add cash value to the user's Coin Balance
                $wallet->increment('coin_balance', $cashToCredit);

                // 2. DEBIT REWARDS: Log a negative reward entry to zero out the converted points
                Reward::create([
                    'user_id' => $user->id,
                    'points' => -$pointsToDebit, // Deducting the converted points
                    'type' => 'DIVIDEND_CONVERSION',
                    'description' => "Quarterly/Half-Yearly Dividend Payout: {$pointsToDebit} points converted to \${$cashToCredit}",
                ]);

                // 3. LOG TRANSACTION: Record the dividend credit transaction
                Transaction::create([
                    'receiver_id' => $user->id,
                    'angel_id' => $user->id, // Angel is receiving their own funds
                    'escrow_amount' => $cashToCredit,
                    'escrow_asset' => 'Coin', // Represents the credited cash value
                    'status' => 'COMPLETED',
                    'payment_method' => 'DIVIDEND_CREDIT',
                    'dispute_reason' => 'Quarterly/Half-Yearly Angel Reward Dividend Credited.',
                ]);

                session()->flash('success', "Success! \${$cashToCredit} in dividends credited to your wallet.");
                $this->loadData(); // Reload data to reflect new balances
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Dividend conversion failed due to a system error: ' . $e->getMessage());
        }
    }

    /**
     * Simulates the withdrawal of cash from the wallet to the user's bank account.
     */
    public function withdrawCash()
    {
        $this->validate();
        $user = Auth::user();
        if (!$user) return;

        if ($this->withdrawalAmount > $this->walletCoinBalance) {
            $this->addError('withdrawalAmount', 'You do not have enough cash-equivalent balance in your wallet to withdraw.');
            return;
        }

        try {
            DB::transaction(function () use ($user) {
                $wallet = $user->wallet;

                // 1. DEBIT WALLET: Deduct the withdrawal amount from the wallet
                $wallet->decrement('coin_balance', $this->withdrawalAmount);

                // 2. LOG TRANSACTION: Record the withdrawal
                Transaction::create([
                    'receiver_id' => $user->id,
                    'angel_id' => $user->id, // Angel is initiating the withdrawal
                    'escrow_amount' => $this->withdrawalAmount,
                    'escrow_asset' => 'Cash', // Represents the fiat cash withdrawn
                    'status' => 'PENDING', // Set to PENDING due to 72-hour delay
                    'payment_method' => 'BANK_WITHDRAWAL',
                    'dispute_reason' => 'Cash Withdrawal to Bank Account.',
                ]);

                session()->flash('success', "Withdrawal of \${$this->withdrawalAmount} initiated. Funds will reflect in your bank account ({$this->bankAccountNumber}) within 72 hours.");
                $this->loadData(); // Reload data
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Cash withdrawal failed due to a system error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.rewards-withdrawal');
    }
}