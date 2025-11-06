<div class="container py-5" wire:init="loadDashboardData" wire:poll.30s="loadDashboardData">
    <h1 class="fw-bold mb-4 text-primary">Welcome to SOQUIC Dashboard</h1>
    <p class="lead mb-5">Quickly manage your assets, transactions, and rewards.</p>

    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white fw-bold h4">
                    Wallet Balances
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3 mb-md-0 border-end">
                            <h2 class="fw-bold mb-0 text-success">${{ number_format($currentCashEquivalentBalance, 2) }}</h2>
                            <small class="text-muted">Available Cash (Coin Balance)</small>
                            <a href="#" class="btn btn-sm btn-outline-primary mt-2">Withdraw</a>
                        </div>

                        <div class="col-md-4 mb-3 mb-md-0 border-end">
                            <h2 class="fw-bold mb-0 text-dark">{{ number_format($wallet->coin_balance ?? 0, 2) }}</h2>
                            <small class="text-muted">SOQUIC Coins</small>
                            <a href="#" class="btn btn-sm btn-outline-info mt-2">Send</a>
                        </div>

                        <div class="col-md-4">
                            <h2 class="fw-bold mb-0 text-dark">{{ number_format($wallet->gift_card_balance ?? 0, 2) }}</h2>
                            <small class="text-muted">SOQUIC Gift Cards</small>
                            <a href="#" class="btn btn-sm btn-outline-info mt-2">Exchange</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header fw-bold bg-warning text-dark">
                    Rewards Overview
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Total Redeemable Points:</strong> <span class="fw-bold text-dark">{{ number_format($totalPoints, 0) }}</span></p>
                    <p class="mb-2">Angel Rewards: <span class="badge bg-success">{{ number_format($stats['angelRewards'], 0) }}</span></p>
                    <p class="mb-2">User (Referral) Rewards: <span class="badge bg-secondary">{{ number_format($stats['userRewards'], 0) }}</span></p>
                    <p class="mb-0">Bonus Rewards: <span class="badge bg-warning text-dark">{{ number_format($stats['bonusRewards'], 0) }}</span></p>
                    <a href="#" class="btn btn-sm btn-warning fw-bold mt-3">Manage Rewards & Dividends</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header fw-bold bg-info text-dark">
                    Cumulative Value & Purchases
                </div>
                <div class="card-body">
                    <p class="mb-2 h3 fw-bold text-success">${{ number_format($stats['cumulativeValue'], 2) }}</p>
                    <small class="text-muted d-block mb-3">Total Estimated Value (Wallet + Convertible Rewards)</small>
                    <p class="mb-2">Lifetime Coins Purchased: ${{ number_format($stats['coinsPurchased'], 2) }}</p>
                    <p class="mb-0">Lifetime Gift Cards Purchased: ${{ number_format($stats['giftCardsPurchased'], 2) }}</p>
                    <a href="#" class="btn btn-sm btn-info mt-3">Purchase More Assets</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white fw-bold h4">
                    Recent Transaction History
                </div>
                <div class="card-body p-0">
                    @if ($recentTransactions->isEmpty())
                        <p class="p-4 text-center text-muted mb-0">No recent transactions found.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($recentTransactions as $transaction)
                                @php
                                    $isCredit = $transaction->receiver_id === Auth::id();
                                    $asset = $transaction->escrow_asset;
                                    $amount = number_format($transaction->escrow_amount, 2);
                                    $badgeClass = $isCredit ? 'bg-success' : 'bg-danger';
                                    $symbol = $isCredit ? '+' : '-';
                                    $type = $transaction->payment_method ?: 'Exchange';
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block">{{ $transaction->created_at->format('M d, Y H:i') }}</small>
                                        <span class="fw-bold">{{ $transaction->dispute_reason ?: $type }}</span>
                                        <span class="badge {{ $transaction->status === 'COMPLETED' ? 'bg-primary' : 'bg-secondary' }} ms-2">{{ $transaction->status }}</span>
                                    </div>
                                    <span class="fw-bold text-{{ $isCredit ? 'success' : 'danger' }}">
                                        {{ $symbol }}${{ $amount }} {{ $asset }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="#" class="btn btn-sm btn-outline-dark">View All Transactions</a>
                </div>
            </div>
        </div>
    </div>
</div>