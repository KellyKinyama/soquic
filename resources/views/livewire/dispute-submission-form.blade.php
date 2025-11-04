<div style="display: {{ $isOpen ? 'block' : 'none' }};" class="modal fade {{ $isOpen ? 'show' : '' }}" tabindex="-1" role="dialog" aria-hidden="{{ $isOpen ? 'false' : 'true' }}">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header bg-danger text-white">
    <h5 class="modal-title fw-bold">Report Exchange Dispute</h5>
    <button type="button" class="btn-close btn-close-white" wire:click="closeModal" aria-label="Close"></button>
    </div>

            <form wire:submit.prevent="submitDispute">
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-2 me-3 rounded-circle bg-danger-subtle border border-danger">
                            <!-- Icon for dispute -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill text-danger" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .964.448.964.954V8.411c0 .51-.428.914-.964.914-.522 0-.964-.404-.964-.914V5.954C7.036 5.448 7.465 5 8 5m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                            </svg>
                        </div>
                        <p class="mb-0 text-muted small">
                            Transaction ID: <span class="fw-bold">{{ $transactionId }}</span>. Please provide a detailed description of the dispute. This action is irreversible.
                        </p>
                    </div>

                    <div class="mb-0">
                        <label for="disputeReason" class="form-label fw-semibold">Reason for Dispute (Minimum 20 Characters)</label>
                        <textarea wire:model.defer="reason" id="disputeReason" rows="5" class="form-control @error('reason') is-invalid @enderror" placeholder="Describe the issue..."></textarea>
                        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submitDispute"
                        class="btn btn-danger fw-bold">
                        <span wire:loading.remove wire:target="submitDispute">Submit Dispute</span>
                        <span wire:loading wire:target="submitDispute" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span wire:loading wire:target="submitDispute"> Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    </div>

    <!-- Manual backdrop for Livewire modal state -->

    @if($isOpen)
    <div class="modal-backdrop fade show"></div>
    @endif