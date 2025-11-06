{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard (Bootstrap)</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            font-family: 'Inter', sans-serif;
        }

        .stat-card {
            color: white;
            padding: 1rem;
            border-radius: 0.75rem;
            /* rounded-3 */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5"> --}}

        <div class="card shadow-lg rounded-4 p-4 p-lg-5">

            <style>
                :root {
                    font-family: 'Inter', sans-serif;
                }

                .stat-card {
                    color: white;
                    padding: 1rem;
                    border-radius: 0.75rem;
                    /* rounded-3 */
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                    transition: transform 0.2s;
                }

                .stat-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                }
            </style>
            <h3 class="fw-bolder text-dark mb-4 border-bottom pb-3">Admin Monitoring Dashboard</h3>

            <!-- Stats Row -->
            <div class="row g-4 mb-5">

                <!-- Total Users -->
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card bg-primary">
                        <p class="text-white-50 mb-1">Total Users</p>
                        <h1 class="fw-bold">{{ $stats['totalUsers'] ?? 0 }}</h1>
                    </div>
                </div>

                <!-- Completed Exchanges -->
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card bg-success">
                        <p class="text-white-50 mb-1">Completed Exchanges</p>
                        <h1 class="fw-bold">{{ $stats['totalCompletedExchanges'] ?? 0 }}</h1>
                    </div>
                </div>

                <!-- Active Escrow Value -->
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card bg-warning text-dark">
                        <p class="text-dark-50 mb-1">Active Escrow Value</p>
                        <h1 class="fw-bold">${{ number_format($stats['totalInProgressEscrow'] ?? 0, 2) }}</h1>
                    </div>
                </div>

                <!-- Total Angels -->
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card bg-info text-dark">
                        <p class="text-dark-50 mb-1">Total Angels</p>
                        <h1 class="fw-bold">{{ $stats['totalAngels'] ?? 0 }}</h1>
                    </div>
                </div>
            </div>

            <!-- Disputes and Leaderboard Row -->
            <div class="row g-5 mb-5">
                <!-- Active Disputes -->
                <div class="col-lg-8">
                    <h3 class="fs-4 fw-bold text-danger mb-4">Active Disputes ({{ $disputedTransactions->count() }})
                    </h3>
                    @if ($disputedTransactions->isEmpty())
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> No active disputes to resolve.
                        </div>
                    @else
                        <div class="table-responsive shadow-sm rounded-3 border border-danger-subtle">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="bg-danger text-white">
                                    <tr>
                                        <th scope="col" class="py-3">ID/Amount</th>
                                        <th scope="col" class="py-3">Receiver</th>
                                        <th scope="col" class="py-3">Angel</th>
                                        <th scope="col" class="py-3">Reason</th>
                                        <th scope="col" class="py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-body-secondary">
                                    @foreach ($disputedTransactions as $transaction)
                                        <tr>
                                            <td class="fw-bold text-dark">
                                                #{{ $transaction->id }}<br>
                                                <span
                                                    class="badge text-bg-warning">${{ number_format($transaction->escrow_amount, 2) }}</span>
                                            </td>
                                            <td>{{ $transaction->receiver->name }}</td>
                                            <td>{{ $transaction->angel->name }}</td>
                                            <td>{{ Str::limit($transaction->dispute_reason, 50) }}</td>
                                            <td class="text-nowrap">
                                                <!-- Action Buttons for Dispute Resolution -->
                                                <button class="btn btn-sm btn-success me-2"
                                                    wire:click="resolveDispute({{ $transaction->id }}, 'complete')"
                                                    wire:confirm="Are you sure you want to COMPLETE transaction #{{ $transaction->id }} (Angel wins)?">
                                                    <i class="bi bi-check-circle"></i> Complete
                                                </button>
                                                <button class="btn btn-sm btn-info text-dark"
                                                    wire:click="resolveDispute({{ $transaction->id }}, 'refund')"
                                                    wire:confirm="Are you sure you want to REFUND transaction #{{ $transaction->id }} (Receiver wins)?">
                                                    <i class="bi bi-arrow-return-left"></i> Refund
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Angel Leaderboard -->
                <div class="col-lg-4">
                    <h3 class="fs-4 fw-bold text-primary mb-4">Top 5 Angels</h3>
                    <ul class="list-group shadow-sm rounded-3">
                        @forelse($angelLeaderboard as $rank => $reward)
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                <div>
                                    <span class="badge text-bg-primary rounded-pill me-2">{{ $rank + 1 }}</span>
                                    <span class="fw-medium text-dark">{{ $reward->user->name ?? 'Unknown' }}</span>
                                </div>
                                <span class="badge text-bg-success">{{ $reward->total_points }} Points</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No rewards tracked yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Recent Transactions -->
            <h3 class="fs-4 fw-bold text-dark mb-4">Recent Activity</h3>
            <div class="table-responsive shadow-sm rounded-3 mb-5">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" class="py-3">ID</th>
                            <th scope="col" class="py-3">Amount</th>
                            <th scope="col" class="py-3">Asset</th>
                            <th scope="col" class="py-3">Status</th>
                            <th scope="col" class="py-3">Receiver</th>
                            <th scope="col" class="py-3">Angel</th>
                            <th scope="col" class="py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-body-secondary">
                        @forelse($recentTransactions as $transaction)
                            <tr>
                                <td class="fw-bold text-dark">{{ $transaction->id }}</td>
                                <td>${{ number_format($transaction->escrow_amount, 2) }}</td>
                                <td>{{ $transaction->escrow_asset }}</td>
                                <td>
                                    <span
                                        class="badge
                                        @if ($transaction->status == 'COMPLETED') text-bg-success
                                        @elseif($transaction->status == 'DISPUTED') text-bg-danger
                                        @elseif($transaction->status == 'REFUNDED') text-bg-info
                                        @else text-bg-warning @endif">
                                        {{ $transaction->status }}
                                    </span>
                                </td>
                                <td>{{ $transaction->receiver->name }}</td>
                                <td>{{ $transaction->angel->name }}</td>
                                <td>{{ $transaction->created_at->format('M d, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No transactions found. Run the
                                    seeders!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- --- ANGEL MANAGEMENT SECTION --- -->
            <hr class="my-5">

            <h2 class="fs-4 fw-bold text-dark mb-4">Angel Management</h2>

            <!-- Flash Message Placeholder -->
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

            <div class="row g-4">
                <!-- Column 1: Regular Users -->
                <div class="col-md-6">
                    <h4 class="fs-5 fw-bold text-secondary">Regular Users ({{ $nonAngels->count() }})</h4>
                    <p class="text-muted small mb-3">Promote a user to Angel status.</p>
                    <div class="list-group shadow-sm border border-secondary-subtle">
                        @forelse($nonAngels as $user)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-dark"><i class="bi bi-person me-2"></i>
                                        {{ $user->name }}</span>
                                    <small class="text-muted d-block d-sm-inline ms-sm-2">{{ $user->email }}</small>
                                </div>
                                <button class="btn btn-sm btn-primary" wire:click="makeAngel({{ $user->id }})">
                                    <i class="bi bi-patch-plus"></i> Make Angel
                                </button>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">All users are already Angels.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Column 2: Current Angels -->
                <div class="col-md-6">
                    <h4 class="fs-5 fw-bold text-secondary">Current Angels ({{ $currentAngels->count() }})</h4>
                    <p class="text-muted small mb-3">Revoke Angel status from a user.</p>
                    <div class="list-group shadow-sm border border-secondary-subtle">
                        @forelse($currentAngels as $user)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-dark"><i
                                            class="bi bi-patch-check-fill text-primary me-2"></i>
                                        {{ $user->name }}</span>
                                    <small class="text-muted d-block d-sm-inline ms-sm-2">{{ $user->email }}</small>
                                </div>
                                <button class="btn btn-sm btn-danger" wire:click="removeAngel({{ $user->id }})">
                                    <i class="bi bi-patch-minus"></i> Remove Angel
                                </button>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">No users are currently Angels.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- --- END ANGEL MANAGEMENT SECTION --- -->

            <!-- --- MANUAL REWARD SECTION --- -->
            <hr class="my-5">

            <h2 class="fs-4 fw-bold text-dark mb-4">Manual Reward Awarding</h2>

            <div class="p-4 bg-success-subtle rounded-3 border border-success">
                <p class="text-success small mb-4">Manually grant rewards (points) to any user for exceptional
                    performance or correction.</p>

                <form wire:submit.prevent="manuallyAwardReward">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="rewardUserId" class="form-label small fw-medium">Select User</label>
                            <select wire:model="rewardUserId" id="rewardUserId"
                                class="form-select @error('rewardUserId') is-invalid @enderror">
                                @foreach ($allUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} (ID:
                                        {{ $user->id }})</option>
                                @endforeach
                            </select>
                            @error('rewardUserId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="rewardPoints" class="form-label small fw-medium">Points</label>
                            <input wire:model="rewardPoints" type="number" id="rewardPoints"
                                class="form-control @error('rewardPoints') is-invalid @enderror" placeholder="20">
                            @error('rewardPoints')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="rewardDescription" class="form-label small fw-medium">Description</label>
                            <input wire:model="rewardDescription" type="text" id="rewardDescription"
                                class="form-control @error('rewardDescription') is-invalid @enderror"
                                placeholder="e.g., Q3 Angel Performance Bonus">
                            @error('rewardDescription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <button type="submit" wire:loading.attr="disabled" wire:target="manuallyAwardReward"
                                class="btn btn-success w-100 fw-bold d-flex align-items-center justify-content-center"
                                style="height: 38px;">
                                <span wire:loading.remove wire:target="manuallyAwardReward"><i
                                        class="bi bi-award me-1"></i> Award</span>
                                <span wire:loading wire:target="manuallyAwardReward"
                                    class="spinner-border spinner-border-sm me-2" role="status"
                                    aria-hidden="true"></span>
                                <span wire:loading wire:target="manuallyAwardReward">Awarding...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- --- END MANUAL REWARD SECTION --- -->

        </div>
    {{-- </div>
    <!-- Bootstrap JS (required for alert dismissal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html> --}}
