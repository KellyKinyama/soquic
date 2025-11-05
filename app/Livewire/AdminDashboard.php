<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Reward;
use App\Models\Wallet; // NEW: Import Wallet Model
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public $stats = [];
    public $recentTransactions = [];
    public $disputedTransactions = [];
    public $angelLeaderboard = [];

    // --- User Management Properties ---
    public $nonAngels = [];
    public $currentAngels = [];

    // --- NEW: Properties for Manual Reward Form ---
    public $rewardUserId = '';
    public $rewardPoints = 0;
    public $rewardDescription = 'Manual Admin Award';


    /**
     * Fetch all the data required for the dashboard.
     */
    public function loadData()
    {
        // --- Application Statistics ---
        $inEscrowAmount = Transaction::whereIn('status', ['IN_PROGRESS', 'DISPUTED'])->sum('escrow_amount');

        $this->stats = [
            'totalUsers' => User::count(),
            'totalAngels' => User::where('is_angel', true)->count(),
            'totalInProgressEscrow' => $inEscrowAmount,
            'totalCompletedExchanges' => Transaction::where('status', 'COMPLETED')->count(),
        ];

        // --- Recent Activity ---
        $this->recentTransactions = Transaction::with(['receiver', 'angel'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // --- Active Disputes ---
        $this->disputedTransactions = Transaction::with(['receiver', 'angel'])
            ->where('status', 'DISPUTED')
            ->orderBy('updated_at', 'desc')
            ->get();

        // --- Angel Leaderboard (Top 5 by Total Rewards) ---
        $this->angelLeaderboard = Reward::select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id')
            ->orderBy('total_points', 'desc')
            ->limit(5)
            ->with('user')
            ->get();

        // --- Load User Lists for Angel Management ---
        $this->nonAngels = User::where('is_angel', false)->orderBy('name')->get();
        $this->currentAngels = User::where('is_angel', true)->orderBy('name')->get();
    }

    // --- User/Angel Management Methods (Existing) ---

    public function makeAngel($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->is_angel = true;
            $user->save();
            $this->loadData();
            session()->flash('message', $user->name . ' has been promoted to an Angel.');
        }
    }

    public function removeAngel($userId)
    {
        if ($userId == 2) {
             session()->flash('error', 'Cannot remove the primary test Angel (ID 2).');
             return;
        }

        $user = User::find($userId);
        if ($user) {
            $user->is_angel = false;
            $user->save();
            $this->loadData();
            session()->flash('message', $user->name . ' has been demoted to a regular user.');
        }
    }


    // --- NEW ADMIN FEATURE 1: DISPUTE RESOLUTION ---

    /**
     * Resolves a disputed transaction by either completing it (Angel wins) or refunding the receiver.
     *
     * @param int $transactionId
     * @param string $resolutionType 'complete' or 'refund'
     */
    public function resolveDispute(int $transactionId, string $resolutionType)
    {
        try {
            DB::transaction(function () use ($transactionId, $resolutionType) {
                // Ensure wallets are loaded for fund transfer
                $transaction = Transaction::with(['receiver.wallet', 'angel.wallet'])->findOrFail($transactionId);

                if ($transaction->status !== 'DISPUTED') {
                    throw new \Exception("Transaction #{$transactionId} is not disputed.");
                }

                $amount = $transaction->escrow_amount;
                $assetType = $transaction->escrow_asset;
                $assetColumn = strtolower($assetType) . '_balance';

                if ($resolutionType === 'complete') {
                    // 1. Angel Wins: Release funds (from implicit escrow) to the Angel
                    $transaction->angel->wallet->increment($assetColumn, $amount);
                    $transaction->update(['status' => 'COMPLETED', 'payment_method' => 'Admin Resolution (Angel Wins)']);

                    // 2. Grant Reward to Angel
                    Reward::create([
                        'user_id' => $transaction->angel_id,
                        'points' => 5, // Small bonus for dispute resolution
                        'type' => 'DISPUTE_WIN',
                        'description' => 'Dispute win reward for transaction #' . $transaction->id,
                    ]);

                    session()->flash('message', "Dispute #{$transactionId} resolved: Funds released to Angel.");

                } elseif ($resolutionType === 'refund') {
                    // 1. Receiver Wins: Return funds (from implicit escrow) to the Receiver
                    $transaction->receiver->wallet->increment($assetColumn, $amount);
                    $transaction->update(['status' => 'REFUNDED', 'payment_method' => 'Admin Resolution (Receiver Wins)']);

                    session()->flash('message', "Dispute #{$transactionId} resolved: Funds refunded to Receiver.");
                } else {
                    throw new \InvalidArgumentException("Invalid resolution type.");
                }

                $this->loadData(); // Refresh the dashboard data
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Dispute resolution failed: ' . $e->getMessage());
        }
    }


    // --- NEW ADMIN FEATURE 2: MANUAL REWARDING ---

    protected $rules = [
        'rewardUserId' => 'required|integer|exists:users,id',
        'rewardPoints' => 'required|integer|min:1',
        'rewardDescription' => 'required|string|max:100',
    ];

    public function manuallyAwardReward()
    {
        $this->validate();

        try {
            Reward::create([
                'user_id' => $this->rewardUserId,
                'points' => $this->rewardPoints,
                'type' => 'ADMIN_AWARD',
                'description' => $this->rewardDescription,
            ]);

            $user = User::find($this->rewardUserId);

            session()->flash('message', "Manually awarded {$this->rewardPoints} points to {$user->name}.");

            // Reset form fields
            $this->reset(['rewardUserId', 'rewardPoints', 'rewardDescription']);
            $this->loadData(); // Refresh leaderboard
        } catch (\Exception $e) {
            session()->flash('error', 'Reward awarding failed: ' . $e->getMessage());
        }
    }

    // --- END NEW ADMIN FEATURES ---

    public function mount()
    {
        // Set default user for the manual reward form (if any user exists)
        $this->rewardUserId = User::first()->id ?? '';
        $this->loadData();
    }

    public function render()
    {
        // Include all users for the reward dropdown selection
        $allUsers = User::orderBy('name')->get();
        return view('livewire.admin-dashboard', compact('allUsers'));
    }
}