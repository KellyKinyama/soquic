<div class="card shadow-lg rounded-3 mx-auto" style="max-width: 600px;">
    <div class="card-body p-4 p-md-5">
    <h2 class="h3 fw-bold text-center mb-4 border-bottom pb-3">Start New Exchange</h2>

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

        <form wire:submit.prevent="initiateExchange">
            <!-- Asset Type Selection -->
            <div class="mb-3">
                <label for="assetType" class="form-label fw-semibold">Asset Type to Exchange</label>
                <select wire:model="assetType" id="assetType" class="form-select form-select-lg @error('assetType') is-invalid @enderror">
                    <option value="Coin">Coin Balance</option>
                    <option value="Gift_Card">Gift Card Balance</option>
                </select>
                @error('assetType') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Exchange Amount -->
            <div class="mb-3">
                <label for="exchangeAmount" class="form-label fw-semibold">Amount (USD Value)</label>
                <input wire:model="exchangeAmount" type="number" id="exchangeAmount" step="0.01" class="form-control form-control-lg @error('exchangeAmount') is-invalid @enderror" placeholder="e.g., 50.00">
                @error('exchangeAmount') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Angel Selection -->
            <div class="mb-4">
                <label for="selectedAngelId" class="form-label fw-semibold">Select Exchange Angel</label>
                <select wire:model="selectedAngelId" id="selectedAngelId" class="form-select form-select-lg @error('selectedAngelId') is-invalid @enderror">
                    @forelse($angels as $angel)
                        <option value="{{ $angel->id }}">{{ $angel->name }} (ID: {{ $angel->id }})</option>
                    @empty
                        <option disabled>No Angels available</option>
                    @endforelse
                </select>
                @error('selectedAngelId') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
                <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="initiateExchange"
                    class="btn btn-primary btn-lg py-3 fw-bold">
                    <span wire:loading.remove wire:target="initiateExchange">Initiate Exchange & Escrow Funds</span>
                    <span wire:loading wire:target="initiateExchange" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span wire:loading wire:target="initiateExchange"> Processing...</span>
                </button>
            </div>
        </form>

        @if ($transactionId)
            <p class="mt-4 text-center text-muted">Transaction ID: <span class="fw-bold text-dark">{{ $transactionId }}</span> has been created.</p>
        @endif
    </div>


    </div>