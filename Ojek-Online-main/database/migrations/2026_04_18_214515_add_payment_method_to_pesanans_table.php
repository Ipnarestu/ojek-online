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
        Schema::table('pesanans', function (Blueprint $table) {
            // Tambahkan kolom payment_method setelah kolom price
            $table->enum('payment_method', ['cod', 'qris', 'wallet'])->default('cod')->after('price');
            
            // Tambahkan kolom payment_status setelah payment_method
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $table->dropColumn(['payment_method', 'payment_status']);
        });
    }
};