<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Pengecekan;
use App\Models\Alat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LogbookController extends Controller
{
    // ============================================================
    // INDEX — Daftar semua logbook (metadata)
    // ============================================================
    public function index(Request $request)
    {
        $opsiJenisLogbook = Logbook::pluck('jenis_logbook')->unique()->filter()->values();
        $opsiJenisAlat    = Logbook::pluck('jenis_alat')->unique()->filter()->values();
        $opsiLokasi       = Logbook::pluck('lokasi_tempat')->unique()->filter()->values();

        $query = Logbook::query();

        if ($request->filled('jenis_logbook') && $request->jenis_logbook !== 'Semua Logbook') {
            $query->where('jenis_logbook', $request->jenis_logbook);
        }

        if ($request->filled('jenis_alat') && $request->jenis_alat !== 'Semua Logbook') {
            $query->where('jenis_alat', $request->jenis_alat);
        }

        if ($request->filled('lokasi') && $request->lokasi !== 'Semua Lokasi') {
            $query->where('lokasi_tempat', $request->lokasi);
        }

        if ($request->filled('search')) {
            $query->where('jenis_logbook', 'like', '%' . $request->search . '%');
        }

        $logbooks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('logbook.index', compact(
            'logbooks',
            'opsiJenisLogbook',
            'opsiJenisAlat',
            'opsiLokasi'
        ));
    }

    // ============================================================
    // SHOW — Detail logbook: tabel harian dari data pengecekan
    // ============================================================
    public function show(Request $request, $id)
    {
        $logbook = Logbook::findOrFail($id);

        // 1. Ambil definisi kolom berdasarkan jenis logbook
        $definisiKolom = Logbook::getDefinisiKolom($logbook->jenis_logbook);

        // 2. Tentukan bulan & tahun yang ditampilkan
        //    Default: bulan ini. Bisa difilter via query ?bulan=2026-05
        $bulanParam = $request->get('bulan', now()->format('Y-m'));
        $bulanCarbon = Carbon::createFromFormat('Y-m', $bulanParam);
        $awalBulan   = $bulanCarbon->copy()->startOfMonth();
        $akhirBulan  = $bulanCarbon->copy()->endOfMonth();
        $jumlahHari  = $bulanCarbon->daysInMonth;

        // 3. Ambil alat yang relevan berdasarkan lokasi logbook
        //    Match nama_alat dengan key di definisiKolom
        $namaAlatTarget = array_keys($definisiKolom);

        $alats = Alat::where('lokasi', 'like', '%' . $logbook->lokasi_tempat . '%')
            ->whereIn('nama_alat', $namaAlatTarget)
            ->get()
            ->keyBy('nama_alat'); // index by nama_alat

        // 4. Ambil semua pengecekan dalam bulan ini untuk alat-alat tersebut
        $semuaPengecekan = Pengecekan::with(['alat', 'user'])
            ->whereHas('alat', function ($q) use ($logbook, $namaAlatTarget) {
                $q->where('lokasi', 'like', '%' . $logbook->lokasi_tempat . '%')
                  ->whereIn('nama_alat', $namaAlatTarget);
            })
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->get();

        // 5. Susun data per tanggal
        //    Struktur: $dataHarian[tanggal][nama_alat] = kondisi
        $dataHarian = [];
        $keteranganHarian = [];
        $teknisiHarian = [];

        for ($hari = 1; $hari <= $jumlahHari; $hari++) {
            $tglStr = $bulanCarbon->copy()->day($hari)->format('Y-m-d');
            $dataHarian[$hari]       = [];
            $keteranganHarian[$hari] = '';
            $teknisiHarian[$hari]    = '';
        }

        foreach ($semuaPengecekan as $cek) {
            $hari      = (int) Carbon::parse($cek->tanggal)->format('j');
            $namaAlat  = $cek->alat->nama_alat ?? '';

            // Isi status kondisi per alat per hari
            if (isset($dataHarian[$hari]) && $namaAlat) {
                $dataHarian[$hari][$namaAlat] = $cek->kondisi_akhir;
            }

            // Isi keterangan & teknisi (ambil dari catatan & user)
            if (!empty($cek->catatan)) {
                $keteranganHarian[$hari] = $cek->catatan;
            }
            if ($cek->user) {
                $teknisiHarian[$hari] = $cek->user->name;
            }
        }

        // 6. Hitung jumlah entri yang terisi (bukan kosong)
        $jumlahTerisi = collect($dataHarian)->filter(fn($d) => !empty($d))->count();

        // 7. Navigasi bulan (prev/next)
        $bulanList = [];
        for ($i = 0; $i < 12; $i++) {
            $bln = now()->copy()->subMonths($i);
            $bulanList[] = [
                'value' => $bln->format('Y-m'),
                'label' => $bln->isoFormat('MMMM YYYY'),
            ];
        }

        return view('logbook.show', compact(
            'logbook',
            'definisiKolom',
            'dataHarian',
            'keteranganHarian',
            'teknisiHarian',
            'jumlahHari',
            'bulanParam',
            'bulanCarbon',
            'jumlahTerisi',
            'bulanList'
        ));
    }

    // ============================================================
    // STORE — Simpan logbook baru (metadata)
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'jenis_logbook'    => 'required|string|max:255',
            'jenis_alat'       => 'required|string|max:255',
            'lokasi_tempat'    => 'required|string|max:255',
            'periode_tersedia' => 'required|string|max:255',
            'jumlah_data'      => 'required|integer|min:0',
        ]);

        Logbook::create([
            'jenis_logbook'      => $request->jenis_logbook,
            'jenis_alat'         => $request->jenis_alat,
            'lokasi_tempat'      => $request->lokasi_tempat,
            'periode_tersedia'   => $request->periode_tersedia,
            'jumlah_data'        => $request->jumlah_data,
            'terakhir_diperbarui' => now(),
        ]);

        return redirect()->route('logbook.index')
            ->with('success', 'Data Logbook baru berhasil ditambahkan!');
    }

    // ============================================================
    // UPDATE — Perbarui metadata logbook
    // ============================================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_logbook'    => 'required|string|max:255',
            'jenis_alat'       => 'required|string|max:255',
            'lokasi_tempat'    => 'required|string|max:255',
            'periode_tersedia' => 'required|string|max:255',
            'jumlah_data'      => 'required|integer|min:0',
        ]);

        $logbook = Logbook::findOrFail($id);
        $logbook->update([
            'jenis_logbook'      => $request->jenis_logbook,
            'jenis_alat'         => $request->jenis_alat,
            'lokasi_tempat'      => $request->lokasi_tempat,
            'periode_tersedia'   => $request->periode_tersedia,
            'jumlah_data'        => $request->jumlah_data,
            'terakhir_diperbarui' => now(),
        ]);

        return redirect()->route('logbook.index')
            ->with('success', 'Data Logbook berhasil diperbarui!');
    }

    // ============================================================
    // DESTROY — Hapus logbook
    // ============================================================
    public function destroy($id)
    {
        $logbook = Logbook::findOrFail($id);
        $logbook->delete();

        return redirect()->route('logbook.index')
            ->with('success', 'Data logbook berhasil dihapus!');
    }
}