<div class="container my-5" style="max-width: 960px;">
    <div class="bg-light p-4 p-md-5 rounded-3 shadow-lg">
    <h1 class="h1 fw-bolder text-primary text-center mb-5">Exchange Status Dashboard</h1>

    @if (session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Transaction ID Input (Bootstrap Input Group) -->
    <div class="input-group input-group-lg mb-5">
        <span class="input-group-text">Transaction ID:</span>
        <input wire:model.live.debounce.500ms="transactionId" type="number" class="form-control" placeholder="e.g., 101">
        <button wire:click="loadTransaction" class="btn btn-secondary" type="button">
            <span wire:loading.remove wire:target="loadTransaction">Load</span>
            <span wire:loading wire:target="loadTransaction" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </button>
    </div>

    @if (!$transaction)
        <div class="card p-5 text-center">
            <p class="text-muted fs-5">Please initiate a new exchange below or enter a Transaction ID to view status.</p>
        </div>
        <!-- Embed the Angel Selection Component if no transaction is loaded -->
        <div class="mt-5">
            <livewire:angel-selection />
        </div>
    @else
        <!-- Transaction Details Card -->
        <div class="card shadow-sm border-light-subtle p-4 p-md-5 mb-5">
            <div class="row g-4 border-bottom pb-4 mb-4">
                <div class="col-md-4">
                    <label class="small text-muted">ID / Status</label>
                    <p class="h5 fw-bold mb-1">{{ $transaction->id }}</p>
                    <span class="badge fs-6
                        @if($transaction->status == 'COMPLETED') bg-success-subtle text-success-emphasis @elseif($transaction->status == 'DISPUTED') bg-danger-subtle text-danger-emphasis @else bg-warning-subtle text-warning-emphasis @endif">
                        {{ $transaction->status }}
                    </span>
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Escrow Amount</label>
                    <p class="h5 fw-bold mb-1">${{ number_format($transaction->escrow_amount, 2) }}</p>
                    <span class="text-muted small">Asset: {{ $transaction->escrow_asset }}</span>
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Angel (Fulfiller)</label>
                    <p class="h5 fw-bold mb-1">{{ $transaction->angel->name ?? 'N/A' }} (ID: {{ $transaction->angel->id ?? 'N/A' }})</p>
                </div>
            </div>

            @if ($transaction->status === 'IN_PROGRESS')
                <div class="alert alert-warning text-center">
                    <p class="h5 mb-0 fw-semibold">Action Required: Confirm Receipt of Cash Payment</p>
                </div>

                <div class="border-top pt-4 mt-4">
                    <h3 class="h4 fw-semibold text-dark mb-4">Payment Actions</h3>
                    <div class="d-grid gap-3 d-sm-flex justify-content-between">
                        <!-- Confirm Receipt Button -->
                        <button wire:click="confirmReceipt"
                            wire:loading.attr="disabled"
                            class="btn btn-success btn-lg flex-grow-1">
                            <span wire:loading.remove wire:target="confirmReceipt">Confirm Cash Received & Release Funds</span>
                            <span wire:loading wire:target="confirmReceipt" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span wire:loading wire:target="confirmReceipt"> Finalizing...</span>
                        </button>

                        <!-- Dispute Button (Triggers Modal/Form) -->
                        <button wire:click="dispatchDisputeForm"
                            class="btn btn-outline-danger btn-lg flex-grow-1">
                            Report Dispute
                        </button>
                    </div>
                </div>
            @elseif ($transaction->status === 'COMPLETED')
                <div class="alert alert-success text-center">
                    <p class="h5 fw-bold mb-0">SUCCESS! Exchange Completed.</p>
                    <p class="mb-0">Funds were released to Angel via {{ $transaction->payment_method ?? 'Cash Confirmation' }}.</p>
                </div>
            @elseif ($transaction->status === 'DISPUTED')
                <div class="alert alert-danger text-center">
                    <p class="h5 fw-bold mb-0">Dispute Reported.</p>
                    <p class="mb-0">Reason: {{ $transaction->dispute_reason ?? 'No reason provided.' }}</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Dispute Modal (always present but hidden by default) -->
    <livewire:dispute-submission-form />


    </div>