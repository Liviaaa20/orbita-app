<?php

namespace App\Http\Controllers;

use App\Models\Kalibrasi;
use App\Models\Kategori;
use App\Models\HistoriOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KalibrasiController extends Controller
{
    /**
     * Ambil nama role user via users.role_id → roles.nama_role
     */
    private function getUserRole(): string
    {
        try {
            $user = Auth::user();
            if (!$user) return '';

            // Load relasi role() BelongsTo → App\Models\Role
            $role = $user->role;
            if ($role && isset($role->nama_role)) {
                return strtolower(trim($role->nama_role));
            }
        } catch (\Throwable $e) {
            //
        }
        return '';
    }

    /** Admin & Teknisi → boleh input */
    private function canInput(): bool
    {
        return in_array($this->getUserRole(), ['admin', 'teknisi']);
    }

    /** Admin, Teknisi, Kepala Kelompok, Koordinator → boleh lihat */
    private function canView(): bool
    {
        return in_array($this->getUserRole(), [
            'admin',
            'teknisi',
            'kepala kelompok',
            'Kepala Kelompok',
            'kepala_kelompok',
            'kapok',
            'koordinator',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        if (!$this->canView()) {
            abort(403, 'Anda tidak memiliki akses ke halaman kalibrasi.');
        }

        $kalibrasis = Kalibrasi::with('kategori')->latest()->get();
        $kategoris  = Kategori::all();
        $canInput   = $this->canInput();

        return view('kalibrasi.index', compact('kalibrasis', 'kategoris', 'canInput'));
    }

    public function store(Request $request)
    {
        if (!$this->canInput()) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data kalibrasi.');
        }

        $request->validate([
            'kategori_id'          => 'required|exists:kategoris,id',
            'tanggal_mulai'        => 'required|date',
            'tanggal_selesai'      => 'required|date|after_or_equal:tanggal_mulai',
            'kalibrator'           => 'required|string|max:255',
            'nilai_koreksi'        => 'nullable|numeric',
            'nilai_ketidakpastian' => 'nullable|numeric',
            'sertifikat_pdf'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'petugas'              => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $pathFile = null;
            if ($request->hasFile('sertifikat_pdf') && $request->file('sertifikat_pdf')->isValid()) {
                $pathFile = $request->file('sertifikat_pdf')
                    ->store('sertifikat_kalibrasi', 'public');
            }

            Kalibrasi::create([
                'kategori_id'          => $request->kategori_id,
                'tanggal_mulai'        => $request->tanggal_mulai,
                'tanggal_selesai'      => $request->tanggal_selesai,
                'kalibrator'           => $request->kalibrator,
                'nilai_koreksi'        => $request->nilai_koreksi,
                'nilai_ketidakpastian' => $request->nilai_ketidakpastian,
                'sertifikat_pdf'       => $pathFile,
                'petugas'              => $request->petugas,
            ]);

            HistoriOperasional::create([
                'kategori_id'     => $request->kategori_id,
                'user_id'         => Auth::id(),
                'jenis_aktivitas' => 'Kalibrasi Alat',
                'waktu'           => now(),
                'kategori'        => 'Maintenance',
                'lokasi'          => 'Laboratorium',
                'deskripsi_hasil' => 'Alat telah dikalibrasi oleh ' . $request->kalibrator
                    . ' dengan nilai koreksi ' . ($request->nilai_koreksi ?? '-'),
                'dokumen'         => $pathFile,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data kalibrasi berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function viewSertifikat($id)
    {
        $kalibrasi = Kalibrasi::findOrFail($id);

        if (!$kalibrasi->sertifikat_pdf) {
            abort(404, 'File sertifikat belum diunggah.');
        }

        $filePath = storage_path('app/public/' . $kalibrasi->sertifikat_pdf);

        if (!file_exists($filePath)) {
            abort(404, 'Berkas tidak ditemukan. Jalankan: php artisan storage:link');
        }

        $ekstensi  = strtolower(pathinfo($kalibrasi->sertifikat_pdf, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];

        return response()->file($filePath, [
            'Content-Type'        => $mimeTypes[$ekstensi] ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="sertifikat_kalibrasi_' . $id . '.' . $ekstensi . '"',
        ]);
    }
}