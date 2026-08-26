<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->integer('jumlah_menit')->default(0)->after('jumlah_jam'); // Total durasi lembur dalam menit (misal: 80 menit = 1 jam 20 menit)
        });
    }

    public function down(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->dropColumn('jumlah_menit');
        });
    }
};
