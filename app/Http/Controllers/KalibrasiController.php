<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kalibrasi;
use App\Models\HistoriOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KalibrasiController extends Controller
{
    public function index()
    {
        // Mengambil data kalibrasi dan daftar alat
        $kalibrasis = Kalibrasi::with('alat')->latest()->get();
        $alats = Alat::all();

        return view('kalibrasi.index', compact('kalibrasis', 'alats'));
    }

    public function store(Request $request)
    {
        // Validasi format data inputan form
        $request->validate([
            'alat_id'              => 'required',
            'tanggal_mulai'        => 'required|date',
            'tanggal_selesai'      => 'required|date|after_or_equal:tanggal_mulai',
            'kalibrator'           => 'required|string|max:255',
            'nilai_koreksi'        => 'required|numeric',
            'nilai_ketidakpastian' => 'required|numeric',
            'sertifikat_pdf'       => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10485', // Ditambahkan ekstensi gambar jika user upload foto
            'petugas'              => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $pathFile = null;
            if ($request->hasFile('sertifikat_pdf')) {
                $pathFile = $request->file('sertifikat_pdf')->store('sertifikat_kalibrasi', 'public');
            }

            // Menyimpan data ke tabel kalibrasis menggunakan kolom 'alat_id' yang valid di database kamu
            $kalibrasi = Kalibrasi::create([
                'alat_id'              => $request->alat_id,
                'tanggal_mulai'        => $request->tanggal_mulai,
                'tanggal_selesai'      => $request->tanggal_selesai,
                'kalibrator'           => $request->kalibrator,
                'nilai_koreksi'        => $request->nilai_koreksi,
                'nilai_ketidakpastian' => $request->nilai_ketidakpastian,
                'sertifikat_pdf'       => $pathFile,
                'petugas'              => $request->petugas,
            ]);

            // Menyimpan log ke tabel histori_operasionals menggunakan kolom 'alat_id'
            HistoriOperasional::create([
                'alat_id'         => $request->alat_id,
                'user_id'         => Auth::id(),
                'jenis_aktivitas' => 'Kalibrasi Alat',
                'waktu'           => now(), // Mengisi tanggal dan jam saat ini ke kolom waktu
                'kategori'        => 'Maintenance', // Bisa disesuaikan dengan kategori di sistemmu
                'lokasi'          => 'Laboratorium', // Bisa disesuaikan dengan lokasi alat
                'deskripsi_hasil' => 'Alat telah dikalibrasi oleh ' . $request->kalibrator . ' dengan nilai koreksi ' . $request->nilai_koreksi,
                'dokumen'         => $pathFile, // Menyimpan path gambar/PDF sertifikat ke histori juga
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data kalibrasi berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan berkas sertifikat (PDF/Gambar) langsung di browser tanpa download otomatis
     */
    public function viewSertifikat($id)
{
    $kalibrasi = Kalibrasi::findOrFail($id);

    // 1. Validasi jika nama file di database ternyata kosong
    if (!$kalibrasi->sertifikat_pdf) {
        abort(404, 'File sertifikat belum diunggah atau tidak ditemukan.');
    }

    // 2. Dapatkan path fisik file di dalam direktori storage (Gunakan path yang benar)
    $filePath = storage_path('app/public/' . $kalibrasi->sertifikat_pdf);

    // 3. Validasi jika fisik file tidak ada di dalam folder storage aplikasi
    if (!file_exists($filePath)) {
        abort(404, 'Berkas fisik sertifikat tidak ditemukan pada server.');
    }

    // 4. Membaca tipe konten berkas secara dinamis (PDF / PNG / JPG)
    $mimeType = mime_content_type($filePath);

    // 5. Alirkan file langsung ke browser dengan header INLINE agar tidak terdownload otomatis
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
    ]);
}
}