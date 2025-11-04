<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Foreign key to the user initiating the exchange (Receiver)
            $table->foreignId('receiver_id')->constrained('users')->onDelete('restrict');

            // Foreign key to the user fulfilling the exchange (Angel)
            $table->foreignId('angel_id')->constrained('users')->onDelete('restrict');

            // Exchange details
            $table->decimal('escrow_amount', 12, 2);
            $table->string('escrow_asset'); // e.g., 'Coin', 'Gift_Card'

            // State management: PENDING_ESCROW, IN_PROGRESS, COMPLETED, DISPUTED
            $table->string('status')->default('PENDING_ESCROW')->index();

            // Finalization and Dispute details (nullable)
            $table->string('payment_method')->nullable(); // e.g., 'Bank Transfer', 'PayPal'
            $table->text('dispute_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
