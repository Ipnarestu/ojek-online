<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->decimal('dest_lat', 10, 8)->nullable()->after('destination');
            $table->decimal('dest_lng', 11, 8)->nullable()->after('dest_lat');
        });
    }

    public function down()
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn(['dest_lat', 'dest_lng']);
        });
    }
};