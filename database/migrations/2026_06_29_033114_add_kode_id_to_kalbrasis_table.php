<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kalibrasis', function (Blueprint $table) {
            // Ditaruh setelah 'id' agar tampil rapi di awal kolom saat di-inspect.
            // nullable() dulu saat kolom baru ditambahkan (supaya tidak gagal
            // jika sudah ada data lama tanpa kode_id), lalu wajib diisi mulai
            // dari sisi validasi controller (request->validate 'required').
            $table->string('kode_id')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('kalibrasis', function (Blueprint $table) {
            $table->dropUnique(['kode_id']);
            $table->dropColumn('kode_id');
        });
    }
};