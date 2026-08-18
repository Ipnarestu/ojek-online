<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Tambahkan kolom currency jika belum ada
            if (!Schema::hasColumn('wallets', 'currency')) {
                $table->string('currency', 3)->default('IDR')->after('balance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};