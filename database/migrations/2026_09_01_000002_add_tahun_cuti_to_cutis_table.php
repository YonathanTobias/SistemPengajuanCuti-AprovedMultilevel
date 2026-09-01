<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->integer('tahun_cuti')->nullable()->after('tanggal_selesai')->index(); // Tahun periode/anggaran cuti (mendukung kelonggaran awal tahun masuk tahun sebelumnya)
        });
    }

    public function down(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropColumn('tahun_cuti');
        });
    }
};
