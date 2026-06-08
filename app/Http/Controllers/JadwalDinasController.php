<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalDinas;
use Carbon\Carbon;

class JadwalDinasController extends Controller
{
    private function getUserRole(): string
    {
        return strtolower(trim(auth()->user()->role->nama_role ?? ''));
    }

    private function bisaLihat(): bool
    {
        return in_array($this->getUserRole(), [
            'admin', 'koordinator', 'kepala kelompok', 'Kepala Kelompok', 'kepala_kelompok', 'kapok', 'teknisi'
        ]);
    }

    private function bisaInput(): bool
    {
        return in_array($this->getUserRole(), [
             'kepala kelompok', 'Kepala Kelompok', 'kepala_kelompok', 'kapok', 'teknisi'
        ]);
    }

    public function index(Request $request)
    {
        if (!$this->bisaLihat()) {
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

        $bisaInput = $this->bisaInput();

        return view('jadwal_dinas.index', compact('jadwals', 'periodeInput', 'bisaInput'));
    }

    public function create()
    {
        if (!$this->bisaInput()) {
            return redirect()->route('jadwal_dinas.index')
                ->with('error', 'Anda tidak memiliki akses untuk menginput jadwal.');
        }

        return view('jadwal_dinas.create');
    }

    public function store(Request $request)
    {
        if (!$this->bisaInput()) {
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

    public function download(Request $request)
    {
        if (!$this->bisaLihat()) {
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