<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>

<body>
    <div class="container my-5">
        <div class="card shadow-lg p-4 p-md-5">
            <h1 class="h2 fw-bolder text-dark mb-4 border-bottom pb-3">Admin Monitoring Dashboard</h1>

            <!-- Stats Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="p-3 bg-primary text-white rounded-3 shadow-sm">
                        <p class="small mb-1 opacity-75">Total Users</p>
                        <h3 class="h3 fw-bold">{{ $stats['totalUsers'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-success text-white rounded-3 shadow-sm">
                        <p class="small mb-1 opacity-75">Completed Exchanges</p>
                        <h3 class="h3 fw-bold">{{ $stats['totalCompletedExchanges'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-warning text-dark rounded-3 shadow-sm">
                        <p class="small mb-1 opacity-75">Active Escrow Value</p>
                        <h3 class="h3 fw-bold">${{ number_format($stats['totalInProgressEscrow'] ?? 0, 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-info text-dark rounded-3 shadow-sm">
                        <p class="small mb-1 opacity-75">Total Angels</p>
                        <h3 class="h3 fw-bold">{{ $stats['totalAngels'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <!-- Disputes and Leaderboard Row -->
            <div class="row g-5 mb-5">
                <!-- Active Disputes -->
                <div class="col-lg-8">
                    <h3 class="h4 fw-bold text-danger mb-3">Active Disputes ({{ $disputedTransactions->count() }})</h3>
                    @if ($disputedTransactions->isEmpty())
                        <div class="alert alert-success">No active disputes to resolve.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-danger">
                                    <tr>
                                        <th>ID</th>
                                        <th>Receiver</th>
                                        <th>Angel</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($disputedTransactions as $transaction)
                                        <tr>
                                            <td class="fw-bold">{{ $transaction->id }}</td>
                                            <td>{{ $transaction->receiver->name }}</td>
                                            <td>{{ $transaction->angel->name }}</td>
                                            <td>{{ Str::limit($transaction->dispute_reason, 50) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Angel Leaderboard -->
                <div class="col-lg-4">
                    <h3 class="h4 fw-bold text-primary mb-3">Top 5 Angels</h3>
                    <ul class="list-group shadow-sm">
                        @forelse($angelLeaderboard as $rank => $reward)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2">{{ $rank + 1 }}</span>
                                    {{ $reward->user->name ?? 'Unknown' }}
                                </div>
                                <span class="badge bg-success rounded-pill">{{ $reward->total_points }} Points</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No rewards tracked yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Recent Transactions -->
            <h3 class="h4 fw-bold mb-3">Recent Activity</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Asset</th>
                            <th>Status</th>
                            <th>Receiver</th>
                            <th>Angel</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                            <tr>
                                <td class="fw-bold">{{ $transaction->id }}</td>
                                <td>${{ number_format($transaction->escrow_amount, 2) }}</td>
                                <td>{{ $transaction->escrow_asset }}</td>
                                <td>
                                    <span
                                        class="badge @if ($transaction->status == 'COMPLETED') bg-success @elseif($transaction->status == 'DISPUTED') bg-danger @else bg-warning text-dark @endif">
                                        {{ $transaction->status }}
                                    </span>
                                </td>
                                <td>{{ $transaction->receiver->name }}</td>
                                <td>{{ $transaction->angel->name }}</td>
                                <td>{{ $transaction->created_at->format('M d, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No transactions found. Run the
                                    seeders!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- --- NEW ANGEL MANAGEMENT SECTION --- -->
            <hr class="my-5">

            <h2 class="h3 fw-bold mb-4">Angel Management</h2>

            <!-- Flash Message -->
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-5">
                <!-- Column 1: Regular Users -->
                <div class="col-md-6">
                    <h4 class="h5 fw-bold">Regular Users ({{ $nonAngels->count() }})</h4>
                    <p class="text-muted small">Promote a user to Angel status.</p>
                    <div class="list-group shadow-sm">
                        @forelse($nonAngels as $user)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-person me-2"></i>
                                    <span class="fw-bold">{{ $user->name }}</span>
                                    <small class="text-muted d-block d-md-inline ms-md-2">{{ $user->email }}</small>
                                </div>
                                <button class="btn btn-primary btn-sm" wire:click="makeAngel({{ $user->id }})">
                                    <i class="bi bi-patch-plus me-1"></i> Make Angel
                                </button>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">All users are already Angels.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Column 2: Current Angels -->
                <div class="col-md-6">
                    <h4 class="h5 fw-bold">Current Angels ({{ $currentAngels->count() }})</h4>
                    <p class="text-muted small">Revoke Angel status from a user.</p>
                    <div class="list-group shadow-sm">
                        @forelse($currentAngels as $user)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-patch-check-fill me-2 text-primary"></i>
                                    <span class="fw-bold">{{ $user->name }}</span>
                                    <small class="text-muted d-block d-md-inline ms-md-2">{{ $user->email }}</all>
                                </div>
                                <button class="btn btn-danger btn-sm" wire:click="removeAngel({{ $user->id }})">
                                    <i class="bi bi-patch-minus me-1"></i> Remove Angel
                                </button>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">No users are currently Angels.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- --- END NEW SECTION --- -->



        </div>
    </div>
</body>

</html>
