<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('cascade');
            $table->string('jabatan');
            $table->string('email')->unique();
            $table->string('no_hp');
            $table->integer('jatah_cuti')->default(12);
            $table->integer('sisa_cuti')->default(12);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
