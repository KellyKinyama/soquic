<div class="card shadow-lg rounded-3 mx-auto" style="max-width: 600px;">
    <div class="card-body p-4 p-md-5">
        <h2 class="h3 fw-bold text-center mb-4 border-bottom pb-3">Send Money Instantly (P2P)</h2>
        <p class="text-center text-muted mb-4">Transfer Coins or Gift Cards to a friend or family member anywhere in the world.</p>

        @if (session()->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if ($successMessage)
            <div class="alert alert-success" role="alert">
                {{ $successMessage }}
            </div>
        @endif

        <div class="row text-center mb-4">
            <div class="col-6 border-end">
                <h4 class="fw-bold mb-0 text-primary">{{ number_format($currentCoinBalance, 2) }}</h4>
                <small class="text-muted">Your Coin Balance</small>
            </div>
            <div class="col-6">
                <h4 class="fw-bold mb-0 text-success">{{ number_format($currentGiftCardBalance, 2) }}</h4>
                <small class="text-muted">Your Gift Card Balance</small>
            </div>
        </div>
        <form wire:submit.prevent="submitTransfer">
            <div class="mb-3">
                <label for="recipientEmail" class="form-label fw-semibold">Recipient's Email Address</label>
                <input wire:model.blur="recipientEmail" type="email" id="recipientEmail" class="form-control form-control-lg @error('recipientEmail') is-invalid @enderror" placeholder="e.g., friend@example.com">
                @error('recipientEmail') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="assetType" class="form-label fw-semibold">Asset to Send</label>
                    <select wire:model="assetType" id="assetType" class="form-select form-select-lg @error('assetType') is-invalid @enderror">
                        <option value="Coin">SOQUIC Coins</option>
                        <option value="Gift_Card">Gift Cards</option>
                    </select>
                    @error('assetType') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="amount" class="form-label fw-semibold">Amount</label>
                    <input wire:model.debounce.500ms="amount" type="number" id="amount" step="0.01" class="form-control form-control-lg @error('amount') is-invalid @enderror" placeholder="e.g., 50.00">
                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-grid mt-4">
                <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="submitTransfer"
                    class="btn btn-primary btn-lg py-3 fw-bold">
                    <span wire:loading.remove wire:target="submitTransfer">Send {{ $assetType }} Now</span>
                    <span wire:loading wire:target="submitTransfer" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span wire:loading wire:target="submitTransfer"> Processing Transfer...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // FIX: Listen for the new dispatch syntax and clear the success message after a delay
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('clear-success-message', () => {
            setTimeout(() => {
                // This clears the message on the Livewire component itself
                @this.set('successMessage', '');
            }, 5000);
        });
    });
</script>