<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalDinas; // Pastikan model ini sudah di-import
use Carbon\Carbon;

class JadwalDinasController extends Controller
{
    // Menampilkan halaman Matriks Jadwal (Bisa dilihat Admin, Teknisi, Kepala Unit)
public function index(Request $request)
{
    // 1. Ambil input 'periode', jika kosong (baru klik menu), otomatis pakai tanggal HARI INI
    $periodeInput = $request->input('periode', Carbon::now()->format('Y-m-d'));
    
    try {
        $date = Carbon::parse($periodeInput);
    } catch (\Exception $e) {
        $date = Carbon::now();
    }

    // 2. Ambil semua data jadwal khusus pada bulan dari tanggal yang aktif 
    // Supaya mode "Mingguan" maupun mode "Bulanan" sama-sama terisi datanya
    $jadwals = JadwalDinas::whereMonth('tanggal', $date->month)
                          ->whereYear('tanggal', $date->year)
                          ->get();

    // 3. Kirim data ke view
    return view('jadwal_dinas.index', compact('jadwals', 'periodeInput'));
}
    // Menampilkan halaman Form Input (Hanya untuk Kepala Unit)
    public function create()
    {
        // Mencegah selain kepala unit masuk lewat ketik URL manual
        $userRole = strtolower(auth()->user()->role->nama_role ?? '');
        if (!in_array($userRole, ['kepala unit', 'kepala_unit'])) {
            return redirect()->route('jadwal_dinas.index')->with('error', 'Anda tidak memiliki akses untuk menginput jadwal.');
        }

        return view('jadwal_dinas.create');
    }

    // Proses menyimpan data dari Form ke Database
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'shift' => 'required|string',
            'jam' => 'required|string',
        ]);

        try {
            JadwalDinas::create([
                'nama' => $request->nama,
                'tanggal' => $request->tanggal,
                'shift' => $request->shift,
                'jam' => $request->jam,
            ]);

            return redirect()->route('jadwal_dinas.index')->with('success', 'Jadwal dinas berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
public function download(Request $request)
{
    $tipe = $request->input('tipe_periode');
    $query = \App\Models\JadwalDinas::query(); 

    if ($tipe === 'mingguan') {
        $mulai = $request->input('tanggal_mulai');
        $selesai = $request->input('tanggal_selesai');
        
        $jadwal = $query->whereBetween('tanggal', [$mulai, $selesai])
                        ->orderBy('tanggal', 'asc')
                        ->get();
                        
        $labelPeriode = "Periode: " . \Carbon\Carbon::parse($mulai)->isoFormat('D MMMM YYYY') . " s/d " . \Carbon\Carbon::parse($selesai)->isoFormat('D MMMM YYYY');
    } else {
        // FIX: Tangkap nilai sesuai name input select bulan yang baru ("YYYY-MM")
        $bulanTahun = $request->input('bulan_pilihan'); 
        $eksplode = explode('-', $bulanTahun);
        $tahun = $eksplode[0];
        $bulan = $eksplode[1];

        $jadwal = $query->whereMonth('tanggal', $bulan)
                        ->whereYear('tanggal', $tahun)
                        ->orderBy('tanggal', 'asc')
                        ->get();
                        
        $labelPeriode = "Periode Bulan: " . \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM YYYY');
    }

    return view('jadwal_dinas.cetak', compact('jadwal', 'labelPeriode'));
}
}