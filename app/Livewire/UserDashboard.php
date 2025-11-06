<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wallet;
use App\Models\Reward;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class UserDashboard extends Component
{
    // Wallet and Rewards Data
    public $wallet;
    public $totalPoints = 0;
    public $currentCashEquivalentBalance = 0.00; // From coin_balance

    // Transaction History
    public $recentTransactions = [];

    // Dashboard Statistics
    public $stats = [
        'coinsPurchased' => 0.00,
        'giftCardsPurchased' => 0.00,
        'angelRewards' => 0,
        'userRewards' => 0, // Placeholder for referral rewards
        'bonusRewards' => 0, // Placeholder for 5-star rating bonuses
        'cumulativeValue' => 0.00, // Total value of all assets/rewards
    ];

    public function mount()
    {
        $this->loadDashboardData();
    }

    /**
     * Loads all necessary user data for the dashboard.
     */
    public function loadDashboardData()
    {
        $user = Auth::user();
        if (!$user) return; // Authentication guard

        // 1. Load Wallet Data
        $this->wallet = $user->wallet;
        $this->currentCashEquivalentBalance = $this->wallet->coin_balance ?? 0.00;

        // 2. Calculate Rewards
        $allRewards = Reward::where('user_id', $user->id)->get();

        $this->stats['angelRewards'] = $allRewards->where('type', 'EXCHANGE_COMPLETED')->sum('points');
        $this->stats['userRewards'] = $allRewards->where('type', 'REFERRAL')->sum('points'); // Assuming a 'REFERRAL' type
        $this->stats['bonusRewards'] = $allRewards->where('type', 'BONUS_RATING')->sum('points'); // Assuming a 'BONUS_RATING' type
        $this->totalPoints = $allRewards->sum('points');

        // 3. Calculate Purchased Totals (requires analyzing transaction history)
        // We look for transactions where the user funded their wallet via BANK_CARD_PURCHASE
        $fundingTransactions = Transaction::where('receiver_id', $user->id)
            ->where('payment_method', 'BANK_CARD_PURCHASE')
            ->get();

        $this->stats['coinsPurchased'] = $fundingTransactions->where('escrow_asset', 'Coin')->sum('escrow_amount');
        $this->stats['giftCardsPurchased'] = $fundingTransactions->where('escrow_asset', 'Gift_Card')->sum('escrow_amount');

        // 4. Calculate Cumulative Value (Example: Cash equivalent + Gift Card balance)
        // Using Coin Balance as cash equivalent for dividend/withdrawal purposes
        $this->stats['cumulativeValue'] =
            ($this->wallet->coin_balance ?? 0.00) +
            ($this->wallet->gift_card_balance ?? 0.00) +
            ($this->totalPoints * \App\Livewire\RewardsWithdrawal::POINTS_TO_CASH_RATE ?? 0);

        // 5. Load Recent Transaction History
        // Fetch transactions where user is either the receiver (credit/exchange) or the angel/sender (debit/transfer)
        $this->recentTransactions = Transaction::with(['receiver', 'angel'])
            ->where(function ($query) use ($user) {
                $query->where('receiver_id', $user->id)
                      ->orWhere('angel_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.user-dashboard');
    }
}