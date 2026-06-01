<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalDinas;
use Carbon\Carbon;

class JadwalDinasController extends Controller
{
    // ── Role yang boleh VIEW & DOWNLOAD ──────────────────────────
    protected $roleBisaLihat = ['admin', 'Admin', 'Koordinator', 'koordinator', 'Kepala Unit', 'kepala unit'];

    // ── Role yang boleh CRUD (input jadwal) ──────────────────────
    protected $roleBisaInput = ['Kepala Unit', 'kepala unit', 'kepala_unit'];

    // ============================================================
    // INDEX — Tampilkan matriks jadwal
    // ============================================================
    public function index(Request $request)
    {
        $userRole = auth()->user()->role->nama_role ?? '';

        // Cek akses: hanya role tertentu yang boleh lihat
        if (!in_array($userRole, $this->roleBisaLihat)) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman Jadwal Dinas.');
        }

        $periodeInput = $request->input('periode', Carbon::now()->format('Y-m-d'));

        try {
            $date = Carbon::parse($periodeInput);
        } catch (\Exception $e) {
            $date = Carbon::now();
        }

        $jadwals = JadwalDinas::whereMonth('tanggal', $date->month)
                              ->whereYear('tanggal', $date->year)
                              ->get();

        // Apakah user boleh input (tampilkan tombol Tambah Jadwal)
        $bisaInput = in_array($userRole, $this->roleBisaInput);

        return view('jadwal_dinas.index', compact('jadwals', 'periodeInput', 'bisaInput'));
    }

    // ============================================================
    // CREATE — Form input jadwal (Kepala Unit saja)
    // ============================================================
    public function create()
    {
        $userRole = auth()->user()->role->nama_role ?? '';

        if (!in_array($userRole, $this->roleBisaInput)) {
            return redirect()->route('jadwal_dinas.index')
                ->with('error', 'Hanya Kepala Unit yang dapat menginput jadwal.');
        }

        return view('jadwal_dinas.create');
    }

    // ============================================================
    // STORE — Simpan jadwal baru
    // ============================================================
    public function store(Request $request)
    {
        $userRole = auth()->user()->role->nama_role ?? '';

        if (!in_array($userRole, $this->roleBisaInput)) {
            return abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'nama'    => 'required|string|max:255',
            'tanggal' => 'required|date',
            'shift'   => 'required|string',
            'jam'     => 'required|string',
        ]);

        try {
            JadwalDinas::create([
                'nama'    => $request->nama,
                'tanggal' => $request->tanggal,
                'shift'   => $request->shift,
                'jam'     => $request->jam,
            ]);

            return redirect()->route('jadwal_dinas.index')
                ->with('success', 'Jadwal dinas berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // DOWNLOAD — Cetak jadwal (Admin, Koordinator, Kepala Unit)
    // ============================================================
    public function download(Request $request)
    {
        $userRole = auth()->user()->role->nama_role ?? '';

        if (!in_array($userRole, $this->roleBisaLihat)) {
            return abort(403, 'Anda tidak memiliki akses untuk mengunduh jadwal.');
        }

        $tipe  = $request->input('tipe_periode');
        $query = JadwalDinas::query();

        if ($tipe === 'mingguan') {
            $mulai   = $request->input('tanggal_mulai');
            $selesai = $request->input('tanggal_selesai');

            $jadwal = $query->whereBetween('tanggal', [$mulai, $selesai])
                            ->orderBy('tanggal', 'asc')
                            ->get();

            $labelPeriode = 'Periode: '
                . Carbon::parse($mulai)->isoFormat('D MMMM YYYY')
                . ' s/d '
                . Carbon::parse($selesai)->isoFormat('D MMMM YYYY');
        } else {
            $bulanTahun = $request->input('bulan_pilihan');
            [$tahun, $bulan] = explode('-', $bulanTahun);

            $jadwal = $query->whereMonth('tanggal', $bulan)
                            ->whereYear('tanggal', $tahun)
                            ->orderBy('tanggal', 'asc')
                            ->get();

            $labelPeriode = 'Periode Bulan: '
                . Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM YYYY');
        }

        return view('jadwal_dinas.cetak', compact('jadwal', 'labelPeriode'));
    }
}