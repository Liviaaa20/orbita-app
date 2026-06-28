<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BARU: kolom 'kode_logbook' — ID logbook yang diisi manual oleh
     * teknisi (misal: LB001, LB002), menggantikan nomor urut otomatis
     * di kolom "No" pada tabel logbook/index.
     *
     * Dibuat nullable supaya data logbook LAMA (yang dibuat sebelum
     * kolom ini ada) tidak rusak — nilainya cuma akan kosong/'-' sampai
     * nanti diisi manual lewat Edit. Kolom ini tetap diberi unique index
     * supaya tidak ada 2 logbook dengan ID yang sama (MySQL/Postgres
     * memperbolehkan banyak baris NULL berdampingan dengan unique index).
     */
    public function up(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->string('kode_logbook', 50)->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropUnique(['kode_logbook']);
            $table->dropColumn('kode_logbook');
        });
    }
};