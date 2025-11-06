<div class="card shadow-lg rounded-3 mx-auto" style="max-width: 600px;" wire:poll.10s="refreshWallet">
    <div class="card-body p-4 p-md-5">
        <h2 class="h3 fw-bold text-center mb-4 border-bottom pb-3">Purchase Coins or Gift Cards</h2>
        <p class="text-center text-muted mb-4">Use your bank card to instantly purchase digital assets for sending or exchanging.</p>

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

        <div class="row text-center mb-4">
            <div class="col-6 border-end">
                <h4 class="fw-bold mb-0 text-primary">{{ number_format($currentCoinBalance, 2) }}</h4>
                <small class="text-muted">Available Coins</small>
            </div>
            <div class="col-6">
                <h4 class="fw-bold mb-0 text-success">{{ number_format($currentGiftCardBalance, 2) }}</h4>
                <small class="text-muted">Available Gift Cards</small>
            </div>
        </div>
        <form wire:submit.prevent="executePurchase">
            <div class="mb-3">
                <label for="assetType" class="form-label fw-semibold">Asset Type to Purchase</label>
                <select wire:model="assetType" id="assetType" class="form-select form-select-lg @error('assetType') is-invalid @enderror">
                    <option value="Coin">SOQUIC Coins</option>
                    <option value="Gift_Card">SOQUIC Gift Cards</option>
                </select>
                @error('assetType') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="purchaseAmount" class="form-label fw-semibold">Amount to Purchase (USD Value)</label>
                <input wire:model.debounce.500ms="purchaseAmount" type="number" id="purchaseAmount" step="1" class="form-control form-control-lg @error('purchaseAmount') is-invalid @enderror" placeholder="e.g., 50.00">
                @error('purchaseAmount') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4 p-3 border rounded bg-light">
                <h6 class="fw-bold mb-2">Payment Method</h6>
                <p class="mb-0 text-muted">Bank Card: {{ $cardDetails }} (Simulated Successful Payment)</p>
                <small class="text-danger">NOTE: This is a **simulated** payment. In a real application, a secure payment gateway integration (e.g., PayFast, Stripe) is mandatory.</small>
            </div>

            <div class="d-grid">
                <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="executePurchase"
                    class="btn btn-primary btn-lg py-3 fw-bold">
                    <span wire:loading.remove wire:target="executePurchase">Complete Purchase & Fund Wallet</span>
                    <span wire:loading wire:target="executePurchase" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span wire:loading wire:target="executePurchase"> Processing Payment...</span>
                </button>
            </div>
        </form>
    </div>
</div>