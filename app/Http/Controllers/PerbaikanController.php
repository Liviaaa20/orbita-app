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
    // ── Semua role yang boleh tambah permintaan (KECUALI admin) ──
    // Disesuaikan dengan nama role di database (case-sensitive!)
    protected $roleBisaInput = [
        'Teknisi',
        'Observer',
        'Tata Usaha',
        'Forecaster',   // ← ditambahkan
        'Kepala Unit',
        'Koordinator',  // ← ditambahkan
    ];

    public function index()
    {
        $perbaikans = Perbaikan::latest()->get();
        return view('perbaikan.index', compact('perbaikans'));
    }

    public function create()
    {
        // Cek role — gunakan tanpa strtolower agar cocok dengan DB
        $userRole = Auth::user()->role->nama_role ?? '';
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
            return redirect()->route('perbaikan.index')->with('success', 'Permintaan berhasil dikirim!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function validasi(Request $request, $id)
    {
        if (Auth::user()->role->nama_role !== 'admin') {
            return abort(403);
        }

        $perbaikan = Perbaikan::findOrFail($id);

        if ($request->action == 'terima') {
            $perbaikan->update(['tgl_diterima' => now()]);
            return back()->with('success', 'Tiket berhasil divalidasi/diterima.');
        } else {
            $perbaikan->update(['tgl_diterima' => null]);
            return back()->with('info', 'Validasi dibatalkan.');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role->nama_role !== 'admin') {
            return abort(403);
        }

        $request->validate([
            'status'  => 'required|in:onproses,selesai',
            'catatan' => 'nullable|string',
        ]);

        $perbaikan = Perbaikan::findOrFail($id);

        $updateData = [
            'catatan' => $request->catatan ?? $perbaikan->catatan,
            'status'  => $request->status,
        ];

        $updateData['tgl_selesai'] = $request->status == 'selesai' ? now() : null;

        $perbaikan->update($updateData);
        return back()->with('success', 'Status dan catatan berhasil diperbarui!');
    }

    public function download($id)
    {
        return response()->json(['message' => 'Fitur cetak laporan segera hadir']);
    }
}