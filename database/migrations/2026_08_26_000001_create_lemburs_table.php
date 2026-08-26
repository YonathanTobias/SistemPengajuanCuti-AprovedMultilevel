<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->integer('saldo_lembur')->default(0)->after('sisa_cuti');
        });

        Schema::create('lemburs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->string('kode_tracking')->unique();
            $table->date('tanggal_lembur');
            $table->integer('jumlah_jam'); // jumlah jam lembur (misal: 1 - 12 jam)
            $table->text('kegiatan'); // kegiatan/alasan lembur
            $table->string('file_bukti')->nullable(); // surat tugas / foto bukti lembur
            $table->enum('status', ['pending_kadiv', 'pending_hrd', 'approved', 'rejected'])->default('pending_kadiv');
            $table->text('catatan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lemburs');
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn('saldo_lembur');
        });
    }
};
