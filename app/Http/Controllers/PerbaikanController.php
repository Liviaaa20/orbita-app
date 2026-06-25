<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Perbaikan;
use App\Models\HistoriOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PerbaikanController extends Controller
{
    protected $roleBisaInput = [
        'Observer',
        'Tata Usaha',
        'Forecaster',
        'Kepala Kelompok',
        'Koordinator',
    ];

    public function index()
    {
        $perbaikans = Perbaikan::with('alat')
            ->latest()
            ->get();

        return view('perbaikan.index', compact('perbaikans'));
    }

    public function create()
    {
        $userRole = Auth::user()->role->nama_role ?? '';

        if (!in_array($userRole, $this->roleBisaInput)) {
            abort(403, 'Otoritas tidak cukup.');
        }

        $alats = Alat::orderBy('nama_alat')->get();

        return view('perbaikan.create', compact('alats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alat_id'            => 'nullable|exists:alats,id',
            'kategori_perbaikan' => 'required|string',
            'keterangan'         => 'required|string',
            'foto'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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

                // workflow awal (TETAP DIPERTAHANKAN)
                'status'             => 'pending',

                // FIX: validasi memang tidak ada di migration → kita biarkan tapi tidak dipakai logic
                'validasi'           => null,

                'validasi_koordinator' => null,
            ];

            if ($request->hasFile('foto')) {
                $data['foto_awal'] = $request->file('foto')
                    ->store('perbaikan/foto_awal', 'public');
            }

            $perbaikan = Perbaikan::create($data);

            HistoriOperasional::create([
                'alat_id'         => $request->alat_id,
                'user_id'         => Auth::id(),
                'jenis_aktivitas' => 'Gangguan',
                'waktu'           => now(),
                'kategori'        => '-',
                'lokasi'          => 'Stasiun Meteorologi Maritim Semarang',
                'deskripsi_hasil' => '[LAPORAN KERUSAKAN] ' . $request->keterangan,
                'dokumen'         => $data['foto_awal'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('perbaikan.index')
                ->with('success', 'Permintaan berhasil dikirim.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |-----------------------------------------
    | VALIDASI ADMIN / TEKNISI
    |-----------------------------------------
    */
    public function validasi(Request $request, $id)
    {
        $role = strtolower(Auth::user()->role->nama_role ?? '');

        if (!in_array($role, ['admin', 'teknisi'])) {
            abort(403);
        }

        $perbaikan = Perbaikan::findOrFail($id);

        // FIX: validasi tetap dipertahankan tapi tidak jadi logic utama
        if ($request->action == 'terima') {

            $perbaikan->update([
                'validasi'     => 1,
                'tgl_diterima' => now(),

                // FIX penting: status harus sinkron migration
                'status'       => 'disetujui'
            ]);

            return back()->with(
                'success',
                'Permintaan berhasil diterima.'
            );
        }

        $perbaikan->update([
            'validasi' => 0,
            'status'   => 'ditolak'
        ]);

        return back()->with(
            'info',
            'Permintaan ditolak.'
        );
    }

    /*
    |-----------------------------------------
    | UPDATE TEKNISI
    |-----------------------------------------
    */
    public function update(Request $request, $id)
    {
        $role = strtolower(Auth::user()->role->nama_role ?? '');

        if (!in_array($role, ['admin', 'teknisi'])) {
            abort(403);
        }

        $request->validate([
            // FIX: ikut migration enum
            'status'        => 'required|in:onproses,menunggu_verifikasi,selesai',
            'catatan'       => 'nullable|string',
            'foto_selesai'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $perbaikan = Perbaikan::findOrFail($id);

        $updateData = [
            'status'  => $request->status,
            'catatan' => $request->catatan,
        ];

        if ($request->hasFile('foto_selesai')) {
            $updateData['foto_selesai'] = $request->file('foto_selesai')
                ->store('perbaikan/foto_selesai', 'public');
        }

        if ($request->status == 'selesai') {
            $updateData['tgl_selesai'] = now();

            // tetap dipertahankan
            $updateData['validasi_koordinator'] = null;
        }

        $perbaikan->update($updateData);

        return back()->with(
            'success',
            'Data perbaikan berhasil diperbarui.'
        );
    }

    /*
    |-----------------------------------------
    | VALIDASI KOORDINATOR
    |-----------------------------------------
    */
    public function validasiKoordinator(Request $request, $id)
    {
        $role = strtolower(Auth::user()->role->nama_role ?? '');

        if ($role != 'koordinator') {
            abort(403);
        }

        $perbaikan = Perbaikan::findOrFail($id);

        if ($request->action == 'setuju') {

            $perbaikan->update([
                'validasi_koordinator' => 1,

                // FIX penting: status ikut selesai
                'status' => 'selesai'
            ]);

            return back()->with(
                'success',
                'Perbaikan telah divalidasi koordinator.'
            );
        }

        $perbaikan->update([
            'validasi_koordinator' => 0,

            // FIX: balik ke proses kerja
            'status' => 'onproses'
        ]);

        return back()->with(
            'warning',
            'Perbaikan dikembalikan ke teknisi.'
        );
    }

    /*
    |-----------------------------------------
    | DOWNLOAD PDF
    |-----------------------------------------
    */
    public function download($id)
    {
        \Carbon\Carbon::setLocale('id');

        $perbaikan = Perbaikan::with('alat')
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'perbaikan.pdf_perbaikan',
            compact('perbaikan')
        );

        return $pdf->download(
            'Laporan_Perbaikan_' .
            $perbaikan->no_tiket .
            '.pdf'
        );
    }
}