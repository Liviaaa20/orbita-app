<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kalibrasis', function (Blueprint $table) {
            $table->id();
            
            // Kolom Relasi ke tabel alats (Pastikan nama tabelnya 'alats' dan primary key-nya 'id')
            $table->foreignId('alat_id')->constrained('alats')->onDelete('cascade');
            
            // Kolom Informasi Kalibrasi
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('kalibrator');
            $table->double('nilai_koreksi', 8, 2);
            $table->double('nilai_ketidakpastian', 8, 2);
            $table->string('sertifikat_pdf')->nullable(); // nullable karena opsional
            $table->string('petugas');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasis');
    }
};
