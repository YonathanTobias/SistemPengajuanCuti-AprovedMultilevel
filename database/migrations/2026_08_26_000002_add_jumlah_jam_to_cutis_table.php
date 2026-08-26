<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->integer('jumlah_jam')->nullable()->after('jumlah_hari'); // Untuk izin jam (Pulang Cepat / Datang Terlambat, maks 3 jam)
        });
    }

    public function down(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropColumn('jumlah_jam');
        });
    }
};
