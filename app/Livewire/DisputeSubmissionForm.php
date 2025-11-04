<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DisputeSubmissionForm extends Component
{
    public $isOpen = false;
    public $transactionId;
    public $reason;

    protected $listeners = ['openDisputeForm'];

    public function openDisputeForm($data)
    {
        $this->transactionId = $data['transactionId'];
        $this->reason = '';
        $this->resetErrorBag();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['transactionId', 'reason']);
    }

    protected $rules = [
        'reason' => 'required|string|min:20',
        'transactionId' => 'required|exists:transactions,id',
    ];

    /**
     * Submits the dispute, updating the transaction status and reason.
     */
    public function submitDispute()
    {
        $this->validate();

        $transaction = Transaction::find($this->transactionId);

        if (!$transaction || $transaction->status !== 'IN_PROGRESS') {
            session()->flash('error', 'Cannot dispute: Transaction not found or already settled.');
            $this->closeModal();
            return;
        }

        try {
            DB::transaction(function () use ($transaction) {
                // --- DISPUTE REPORTING LOGIC ---
                $transaction->update([
                    'status' => 'DISPUTED',
                    'dispute_reason' => $this->reason,
                ]);

                session()->flash('success', 'Dispute reported successfully. A moderator will review it shortly.');
                $this->closeModal();
                $this->dispatch('transactionUpdated'); // Notify ExchangeInitiator to refresh its view
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
