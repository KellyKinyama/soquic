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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            // Link to the user who earned the reward (Angel)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->integer('points')->unsigned();
            $table->string('type'); // e.g., 'EXCHANGE_COMPLETED', 'REFERRAL'
            $table->string('description');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
