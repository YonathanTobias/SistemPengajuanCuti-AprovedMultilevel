<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->integer('jumlah_menit')->nullable()->after('jumlah_jam'); // Total menit izin (misal: 90 menit = 1 jam 30 menit)
        });
    }

    public function down(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropColumn('jumlah_menit');
        });
    }
};
