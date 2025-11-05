<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public $stats = [];
    public $recentTransactions = [];
    public $disputedTransactions = [];
    public $angelLeaderboard = [];

    // --- NEW PROPERTIES ---
    public $nonAngels = [];
    public $currentAngels = [];
    // --- END NEW PROPERTIES ---

    /**
     * Fetch all the data required for the dashboard.
     */
    public function loadData()
    {
        // --- Application Statistics ---
        $this->stats = [
            'totalUsers' => User::count(),
            'totalAngels' => User::where('is_angel', true)->count(),
            'totalInProgressEscrow' => Transaction::where('status', 'IN_PROGRESS')->sum('escrow_amount'),
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

        // --- NEW: Load User Lists for Angel Management ---
        $this->nonAngels = User::where('is_angel', false)->orderBy('name')->get();
        $this->currentAngels = User::where('is_angel', true)->orderBy('name')->get();
    }

    // --- NEW METHOD: Promote a user to Angel ---
    public function makeAngel($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->is_angel = true;
            $user->save();
            $this->loadData(); // Refresh all data on the dashboard
            session()->flash('message', $user->name . ' has been promoted to an Angel.');
        }
    }

    // --- NEW METHOD: Demote an Angel to user ---
    public function removeAngel($userId)
    {
        // Safety check: Prevent removing the primary seeded Angel (ID 2)
        if ($userId == 2) {
             session()->flash('error', 'Cannot remove the primary test Angel (ID 2).');
             return;
        }

        $user = User::find($userId);
        if ($user) {
            $user->is_angel = false;
            $user->save();
            $this->loadData(); // Refresh all data on the dashboard
            session()->flash('message', $user->name . ' has been demoted to a regular user.');
        }
    }
    // --- END NEW METHODS ---

    public function mount()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}