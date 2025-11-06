<div class="card shadow-lg rounded-3 mx-auto" style="max-width: 800px;" wire:init="loadData">
    <div class="card-body p-4 p-md-5">
        <h2 class="h3 fw-bold text-center mb-4 border-bottom pb-3 text-primary">Angel Rewards & Withdrawal Center</h2>
        <p class="text-center text-muted mb-4">Manage your earned points, convert them to cash dividends, and withdraw
            funds to your bank account.</p>

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

        <div class="row text-center mb-5 p-3 rounded-3 bg-light border">
            <div class="col-md-4 mb-3 mb-md-0">
                <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalPoints, 0) }}</h4>
                <small class="text-muted">Total Points Earned</small>
            </div>
            <div class="col-md-4 mb-3 mb-md-0 border-start border-end">
                <h4 class="fw-bold mb-0 text-success">${{ number_format($convertableCashValue, 2) }}</h4>
                <small class="text-muted">Available Dividend Value</small>
            </div>
            <div class="col-md-4">
                <h4 class="fw-bold mb-0 text-primary">${{ number_format($walletCoinBalance, 2) }}</h4>
                <small class="text-muted">Current Withdraw-able Wallet Balance (Cash Equivalent)</small>
            </div>
        </div>

        <h4 class="fw-bold mb-3">1. Convert Rewards to Cash Dividend</h4>
        <div class="p-3 border rounded-3 mb-5">
            <p>Your current **{{ number_format($totalPoints, 0) }} points** are convertible to
                **${{ number_format($convertableCashValue, 2) }}**. This cash value will be credited to your
                withdraw-able wallet balance.</p>
            <small class="text-info d-block mb-3">Conversion Rate: 1 Point =
                ${{ number_format(self::POINTS_TO_CASH_RATE, 2) }}.</small>

            <button wire:click="convertPointsToCash" wire:loading.attr="disabled" wire:target="convertPointsToCash"
                class="btn btn-success fw-bold py-2" @if ($totalPoints <= 0) disabled @endif>
                <span wire:loading.remove wire:target="convertPointsToCash">Convert {{ number_format($totalPoints, 0) }}
                    Points Now</span>
                <span wire:loading wire:target="convertPointsToCash" class="spinner-border spinner-border-sm"
                    role="status" aria-hidden="true"></span>
                <span wire:loading wire:target="convertPointsToCash"> Processing Conversion...</span>
            </button>
        </div>

        <h4 class="fw-bold mb-3">2. Withdraw Cash to Bank Account</h4>
        <form wire:submit.prevent="withdrawCash" class="p-3 border rounded-3">
            <div class="mb-3">
                <label for="withdrawalAmount" class="form-label fw-semibold">Amount to Withdraw (Max:
                    ${{ number_format($walletCoinBalance, 2) }})</label>
                <input wire:model.debounce.500ms="withdrawalAmount" type="number" id="withdrawalAmount" step="0.01"
                    class="form-control form-control-lg @error('withdrawalAmount') is-invalid @enderror"
                    placeholder="Enter amount">
                @error('withdrawalAmount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-warning small">
                <p class="mb-1 fw-semibold">Destination Account:</p>
                <p class="mb-0">Bank Account: (***{{ $bankAccountNumber }})</p>
                <p class="mb-0">Processing Time: Funds will be paid directly to your bank account within **72 hours**.
                </p>
            </div>


            <button type="submit" wire:loading.attr="disabled" wire:target="withdrawCash"
                class="btn btn-primary btn-lg py-2 fw-bold" @if ($walletCoinBalance < 10) disabled @endif>

                <span wire:loading.remove wire:target="withdrawCash">
                    Confirm Withdrawal of
                    @if ($withdrawalAmount > 0)
                        ${{ number_format($withdrawalAmount, 2) }}
                    @else
                        Amount
                    @endif
                </span>
                <span wire:loading wire:target="withdrawCash" class="spinner-border spinner-border-sm" role="status"
                    aria-hidden="true"></span>
                <span wire:loading wire:target="withdrawCash"> Processing Withdrawal...</span>
            </button>
        </form>
    </div>
</div>
