<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MasterData\KategoriController;
use App\Http\Controllers\MasterData\SubKategoriController;
use App\Http\Controllers\MasterData\AlatController;
use App\Http\Controllers\MasterData\PengecekanController;
use App\Http\Controllers\HistoriOperasionalController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PerbaikanController;

// --- AUTHENTICATION ---
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// --- PROTECTED ROUTES (HARUS LOGIN) ---
Route::middleware(['auth'])->group(function () {
    
    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // Manajemen Akses & User
    Route::resource('role', RoleController::class);
    Route::resource('user', UserController::class)->except(['show']);

    // Master Data
    Route::resource('kategori', KategoriController::class);
    Route::resource('sub-kategori', SubKategoriController::class);
// Master Data
Route::get('/get-sub-kategori/{kategori_id}', [AlatController::class, 'getSubKategori'])->name('get-sub-kategori');
Route::resource('data-alat', AlatController::class);    // Pengecekan
    Route::get('/pengecekan', [PengecekanController::class, 'index'])->name('pengecekan.index');
    Route::post('/pengecekan/store', [PengecekanController::class, 'store'])->name('pengecekan.store');
    Route::get('/pengecekan', [MaintenanceController::class, 'showPengecekan'])->name('pengecekan.index');

/// --- MAINTENANCE (HARIAN & MINGGUAN) ---
Route::prefix('maintenance')->name('maintenance.')->group(function () {
    // Pastikan mengarah ke showPengecekan
    Route::get('/harian', [MaintenanceController::class, 'indexHarian'])->name('harian');
    Route::get('/mingguan', [MaintenanceController::class, 'indexMingguan'])->name('mingguan');
    
    // Halaman pilih lokasi
    Route::get('/form-pengecekan', [MaintenanceController::class, 'showPengecekan'])->name('show-pengecekan');
    Route::get('/form-pengecekan-alt', [MaintenanceController::class, 'showPengecekan'])->name('showPengecekan');

    // TAHAP 2: Simpan Inisiasi (Plotting)
    Route::post('/store-inisiasi', [MaintenanceController::class, 'storeInisiasi'])->name('store-inisiasi');
    
    // TAHAP 3: Form Checklist Fisik
    Route::get('/form-master', [MaintenanceController::class, 'formMaster'])->name('form-master');
    
    // TAHAP 4: Simpan Hasil Akhir
    Route::post('/store-hasil', [MaintenanceController::class, 'storeHasilFisik'])->name('store-hasil');
    Route::post('/store', [MaintenanceController::class, 'storeInisiasi'])->name('store');
});

    // Group Route untuk Permintaan Perbaikan
    Route::prefix('perbaikan')->name('perbaikan.')->group(function () {
    // Halaman Utama Tabel Perbaikan
    Route::get('/', [PerbaikanController::class, 'index'])->name('index');

    // --- TAMBAHKAN DUA BARIS INI (BARU) ---
    Route::get('/create', [PerbaikanController::class, 'create'])->name('create'); // Menampilkan form
    Route::post('/store', [PerbaikanController::class, 'store'])->name('store');   // Proses simpan data
    // --------------------------------------
    
    // Route untuk Validasi Cepat (Tombol Centang/Silang)
    Route::post('/validasi/{id}', [PerbaikanController::class, 'validasi'])->name('validasi');    
    // Route untuk Update Catatan & Status (Simpan Modal)
    Route::put('/update/{id}', [PerbaikanController::class, 'update'])->name('update');
    
    // Route untuk Download Laporan (Opsional)
    Route::get('/download/{id}', [PerbaikanController::class, 'download'])->name('download');
    });

    // --- HISTORI OPERASIONAL (Mandiri) ---
    // Halaman Utama & Filter
    Route::get('/histori-operasional', [HistoriOperasionalController::class, 'index'])->name('histori.index');
    
    // Export Laporan Keseluruhan (Tombol Unduh di atas tabel)
    Route::get('/histori-operasional/export', [HistoriOperasionalController::class, 'export'])->name('histori.export');
    
    // Detail & Download Per Baris (Tombol di kolom Aksi)
    Route::get('/histori-operasional/{id}', [HistoriOperasionalController::class, 'show'])->name('histori.show');
    Route::get('/histori-operasional/download/{id}', [HistoriOperasionalController::class, 'downloadSingle'])->name('histori.download-single');
    Route::get('/histori/{id}/riwayat', [HistoriOperasionalController::class, 'downloadRiwayat'])->name('histori.riwayat');
});