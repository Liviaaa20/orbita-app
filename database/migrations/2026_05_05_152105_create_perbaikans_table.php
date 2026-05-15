<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::create('perbaikans', function (Blueprint $table) {
        $table->id();
        $table->string('no_tiket'); 
        $table->string('foto')->nullable();
        $table->timestamp('tgl_permintaan')->nullable();
        $table->timestamp('tgl_diterima')->nullable();
        $table->timestamp('tgl_selesai')->nullable();
        $table->string('user'); 
        
        // Kategori di wireframe berisi detail alat/lokasi
        $table->text('kategori_perbaikan'); 
        $table->text('keterangan');
        
        // Diubah ke nullable agar ada status "Pending" (belum divalidasi)
        $table->boolean('validasi')->nullable(); 
        
        $table->text('catatan')->nullable();
        
        // Tambahkan 'pending' sebagai default
        $table->enum('status', ['pending', 'onproses', 'selesai'])->default('pending');
        
        $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbaikans');
    }
};
