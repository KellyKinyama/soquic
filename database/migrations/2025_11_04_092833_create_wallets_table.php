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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            // Link to the user who owns this wallet. One-to-one relationship.
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

            // Balances for the two primary asset types, using precision for currency.
            $table->decimal('coin_balance', 12, 2)->default(0.00);
            $table->decimal('gift_card_balance', 12, 2)->default(0.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
