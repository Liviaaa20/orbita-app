<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Perbaikan;
use App\Models\HistoriOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PerbaikanController extends Controller
{
    protected $roleBisaInput = ['teknisi', 'kepala unit', 'observer', 'forcaster', 'tata usaha'];

    public function index()
    {
        // Langsung ambil semua data tanpa membedakan role
        $perbaikans = Perbaikan::latest()->get();

        return view('perbaikan.index', compact('perbaikans'));
    }

    public function create()
    {
        $userRole = strtolower(Auth::user()->role->nama_role ?? '');
        if (!in_array($userRole, $this->roleBisaInput)) {
            return abort(403, 'Otoritas tidak cukup.');
        }

        $alats = Alat::orderBy('nama_alat', 'asc')->get();
        return view('perbaikan.create', compact('alats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alat_id'            => 'nullable|exists:alats,id',
            'kategori_perbaikan' => 'required',
            'keterangan'         => 'required',
            'foto'               => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'no_tiket'           => 'TKT-' . strtoupper(uniqid()),
                'alat_id'            => $request->alat_id,
                'tgl_permintaan'     => now(),
                'user'               => Auth::user()->name,
                'kategori_perbaikan' => $request->kategori_perbaikan,
                'keterangan'         => $request->keterangan,
                'status'             => 'onproses',
            ];

            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('perbaikan', 'public');
            }

            Perbaikan::create($data);

            // Log ke Histori Operasional
            HistoriOperasional::create([
                'alat_id'         => $request->alat_id,
                'user_id'         => Auth::id(),
                'jenis_aktivitas' => 'Gangguan',
                'waktu'           => now(),
                'kategori'        => '-',
                'lokasi'          => 'Stasiun Meteorologi Maritim Semarang',
                'deskripsi_hasil' => '[LAPORAN KERUSAKAN] ' . $request->keterangan,
                'dokumen'         => $data['foto'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('perbaikan.index')->with('success', 'Permintaan terkirim!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * FUNGSI VALIDASI (Khusus Tombol Centang/Silang)
     */
    public function validasi(Request $request, $id)
    {
        if (strtolower(Auth::user()->role->nama_role ?? '') !== 'admin') {
            return abort(403);
        }

        $perbaikan = Perbaikan::findOrFail($id);
        
        if ($request->action == 'terima') {
            $perbaikan->update([
                'tgl_diterima' => now(), // MENGISI TANGGAL TERIMA SAAT DICENTANG
            ]);
            return back()->with('success', 'Tiket berhasil divalidasi/diterima.');
        } else {
            // Jika disilang (tolak/batal), kosongkan tgl_diterima
            $perbaikan->update([
                'tgl_diterima' => null,
            ]);
            return back()->with('info', 'Validasi dibatalkan.');
        }
    }

    /**
     * FUNGSI UPDATE STATUS & CATATAN (Dropdown)
     */
    public function update(Request $request, $id)
    {
        if (strtolower(Auth::user()->role->nama_role ?? '') !== 'admin') {
            return abort(403);
        }

        $request->validate([
            'status'  => 'required|in:onproses,selesai',
            'catatan' => 'nullable|string'
        ]);

        $perbaikan = Perbaikan::findOrFail($id);
        
        $updateData = [
            'catatan' => $request->catatan ?? $perbaikan->catatan,
            'status'  => $request->status,
        ];

        // LOGIKA TANGGAL SELESAI
        if ($request->status == 'selesai') {
            $updateData['tgl_selesai'] = now(); // ISI TANGGAL SELESAI PAS STATUS SELESAI
        } else {
            $updateData['tgl_selesai'] = null; // KOSONGKAN JIKA BALIK KE PROSES
        }

        $perbaikan->update($updateData);

        return back()->with('success', 'Status dan catatan berhasil diperbarui!');
    }

    public function download($id)
    {
        return response()->json(['message' => 'Fitur cetak laporan segera hadir']);
    }
}