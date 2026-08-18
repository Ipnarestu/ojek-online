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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wallet_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'topup',
                'withdraw',
                'order_payment',
                'order_income',
                'platform_fee',
                'refund',
                'payment',      // ← TAMBAHKAN untuk pembayaran order
                'tip',          // ← TAMBAHKAN untuk tip driver
            ]);

            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2)->nullable();  // ← TAMBAHKAN
            $table->decimal('balance_after', 15, 2)->nullable();   // ← TAMBAHKAN
            $table->string('reference')->nullable();
            $table->string('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};