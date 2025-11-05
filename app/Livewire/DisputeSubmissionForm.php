<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DisputeSubmissionForm extends Component
{
    // Properties for modal state and data
    public $isOpen = false;
    public $transactionId;
    public $reason = '';

    // Listen for the event dispatched from ExchangeInitiator
    protected $listeners = ['openDisputeForm'];

    // Validation rules for the dispute reason
    protected $rules = [
        'reason' => 'required|string|min:20|max:500',
    ];

    /**
     * Listener method triggered by Livewire.dispatch('openDisputeForm', { ... }).
     * FIX: Accepts the payload as a generic $data array to resolve dependency issue.
     */
    public function openDisputeForm(array $data)
    {
        // Extract the transaction ID safely from the passed array payload
        $this->transactionId = $data['transactionId'] ?? null;

        // Reset state for new dispute submission
        $this->resetValidation();
        $this->reason = '';
        $this->isOpen = true;
    }

    /**
     * Closes the modal and resets properties.
     */
    public function closeModal()
    {
        $this->isOpen = false;
        $this->transactionId = null;
        $this->reason = '';
    }

    /**
     * Submits the dispute, updates the transaction status, and closes the modal.
     * This method contains the core DISPUTE business logic.
     */
    public function submitDispute()
    {
        $this->validate();

        if (!$this->transactionId) {
            session()->flash('error', 'Error: No transaction ID provided for dispute.');
            return;
        }

        try {
            DB::transaction(function () {
                $transaction = Transaction::findOrFail($this->transactionId);

                // Check status to ensure it can be disputed
                if ($transaction->status !== 'IN_PROGRESS') {
                    throw new \Exception('Transaction cannot be disputed as it is not currently in progress.');
                }

                // Update the transaction
                $transaction->update([
                    'status' => 'DISPUTED',
                    'dispute_reason' => $this->reason,
                ]);

                session()->flash('success', 'Dispute for transaction #' . $this->transactionId . ' has been reported.');

                // Dispatch event to the parent/initiator component to refresh its data
                $this->dispatch('transactionUpdated');

                $this->closeModal();
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Dispute submission failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dispute-submission-form');
    }
}