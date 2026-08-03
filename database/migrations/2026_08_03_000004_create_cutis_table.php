<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tracking')->unique();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->string('jenis_cuti');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_hari');
            $table->text('alasan');
            $table->text('alamat_cuti');
            $table->string('no_hp_cuti');
            $table->string('file_pendukung')->nullable();
            
            // Statuses: 'pending_kadiv', 'pending_hrd', 'pending_ketua', 'approved', 'rejected'
            $table->string('status')->default('pending_kadiv');
            
            $table->text('catatan_kadiv')->nullable();
            $table->timestamp('kadiv_approved_at')->nullable();
            
            $table->text('catatan_hrd')->nullable();
            $table->timestamp('hrd_approved_at')->nullable();
            
            $table->text('catatan_ketua')->nullable();
            $table->timestamp('ketua_approved_at')->nullable();
            
            $table->string('rejected_by')->nullable(); // 'kadiv', 'hrd', 'ketua'
            $table->text('catatan_penolakan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cutis');
    }
};
