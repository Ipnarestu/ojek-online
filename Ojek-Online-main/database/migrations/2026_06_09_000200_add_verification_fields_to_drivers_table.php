<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Dokumen driver
            $table->string('ktp_path')->nullable()->after('phone');
            $table->string('sim_path')->nullable()->after('ktp_path');
            $table->string('stnk_path')->nullable()->after('sim_path');
            $table->string('photo_path')->nullable()->after('stnk_path');
            
            // Status verifikasi
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->after('online');
            $table->text('rejection_reason')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('rejection_reason');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
        });
    }

    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn([
                'ktp_path', 'sim_path', 'stnk_path', 'photo_path',
                'verification_status', 'rejection_reason', 'verified_at', 'verified_by'
            ]);
        });
    }
};