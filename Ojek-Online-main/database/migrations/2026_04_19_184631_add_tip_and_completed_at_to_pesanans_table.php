<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->decimal('tip_amount', 10, 2)->default(0)->after('price');
            $table->timestamp('completed_at')->nullable()->after('user_notified');
        });
    }

    public function down()
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn(['tip_amount', 'completed_at']);
        });
    }
};