<?php

namespace App\Http\Controllers;

use App\Models\HistoriOperasional;
use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class HistoriOperasionalController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::all();
        $alats = Alat::all();
        $jenisAktivitas = ['Maintenance Harian', 'Maintenance Mingguan', 'Kalibrasi', 'Gangguan', 'Pengujian', 'Lainnya'];
    
        // --- QUERY 1: Dari Tabel Histori ---
        $queryHistori = DB::table('histori_operasionals as h')
            ->leftJoin('alats as a', 'h.alat_id', '=', 'a.id')
            ->leftJoin('sub_kategoris as sk', 'a.sub_kategori_id', '=', 'sk.id')
            ->leftJoin('kategoris as k', 'sk.kategori_id', '=', 'k.id')
            ->leftJoin('users as u', 'h.user_id', '=', 'u.id')
            ->select(
                'h.id',
                'h.waktu as tanggal_raw',
                'h.jenis_aktivitas',
                'a.nama_alat',
                'k.nama_kategori',
                'sk.nama_sub_kategori',
                'a.lokasi',
                'h.deskripsi_hasil as deskripsi',
                'u.name as petugas',
                'h.dokumen',
                'h.alat_id'
            );
    
        // --- QUERY 2: Dari Tabel Pengecekan ---
        $queryPengecekan = DB::table('pengecekans as p')
            ->leftJoin('alats as a', 'p.alat_id', '=', 'a.id')
            ->leftJoin('sub_kategoris as sk', 'a.sub_kategori_id', '=', 'sk.id')
            ->leftJoin('kategoris as k', 'sk.kategori_id', '=', 'k.id')
            ->leftJoin('users as u', 'p.user_id', '=', 'u.id')
            ->select(
                'p.id',
                'p.tanggal as tanggal_raw',
                DB::raw("CASE WHEN p.waktu IN ('Pagi', 'Siang', 'Malam') THEN 'Maintenance Harian' ELSE 'Maintenance Mingguan' END as jenis_aktivitas"),
                'a.nama_alat',
                'k.nama_kategori',
                'sk.nama_sub_kategori',
                'a.lokasi',
                DB::raw("CONCAT('Kondisi: ', p.kondisi_akhir) as deskripsi"),
                'u.name as petugas',
                'p.foto_kegiatan as dokumen',
                'p.alat_id'
            );
    
        // Menggabungkan Query
        $combined = $queryHistori->unionAll($queryPengecekan);
    
        // Membungkus dalam subquery agar filter berfungsi global
        $finalQuery = DB::table(DB::raw("({$combined->toSql()}) as combined_table"))
            ->mergeBindings($combined);
    
        // Logika Filter
        if ($request->filled('periode')) {
            $range = explode(' - ', $request->periode);
            try {
                $start = Carbon::createFromFormat('d/m/Y', trim($range[0]))->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', trim($range[1]))->endOfDay();
                $finalQuery->whereBetween('tanggal_raw', [$start, $end]);
            } catch (\Exception $e) {}
        }
        
        if ($request->filled('jenis_aktivitas')) {
            $finalQuery->where('jenis_aktivitas', $request->jenis_aktivitas);
        }
        if ($request->filled('alat_id')) {
            $finalQuery->where('alat_id', $request->alat_id);
        }
        if ($request->filled('kategori')) {
            $finalQuery->where('nama_kategori', $request->kategori);
        }
        if ($request->filled('lokasi')) {
            $finalQuery->where('lokasi', $request->lokasi);
        }
    
        // Eksekusi Pagination
        $histori = $finalQuery->orderBy('tanggal_raw', 'desc')->paginate(15);
    
        // Hitung summary dari query yang sudah terfilter (Gunakan clone agar tidak merusak query utama)
        $summaryQuery = clone $finalQuery;
        $summary = [
            'total' => $summaryQuery->count(),
            'maintenance_harian' => (clone $summaryQuery)->where('jenis_aktivitas', 'Maintenance Harian')->count(),
            'maintenance_mingguan' => (clone $summaryQuery)->where('jenis_aktivitas', 'Maintenance Mingguan')->count(),
            'kalibrasi' => (clone $summaryQuery)->where('jenis_aktivitas', 'Kalibrasi')->count(),
            'gangguan' => (clone $summaryQuery)->where('jenis_aktivitas', 'Gangguan')->count(),
            'lainnya' => (clone $summaryQuery)->where('jenis_aktivitas', 'Lainnya')->count(),
        ];
    
        return view('histori.index', compact('histori', 'kategoris', 'alats', 'jenisAktivitas', 'summary'));
    }

    public function show($id, Request $request)
{
    $jenis = $request->query('jenis');

    if ($jenis == 'Maintenance Harian' || $jenis == 'Maintenance Mingguan') {
        $data = \App\Models\Pengecekan::with(['alat.subKategori.kategori', 'user'])->findOrFail($id);
        
        // JANGAN parse kolom 'waktu' jika isinya 'Pagi/Siang/Malam'
        // Kita simpan string aslinya saja
        $data->shift = $data->waktu; 
        
        // Untuk tanggal, baru kita parse
        $data->tanggal_display = \Carbon\Carbon::parse($data->tanggal)->format('d F Y');
    } else {
        $data = HistoriOperasional::with(['alat.subKategori.kategori', 'user'])->findOrFail($id);
        
        // Di tabel histori, kolom 'waktu' biasanya berisi datetime (2026-05-15 10:00)
        // Ini aman untuk di-parse
        $data->tanggal_display = \Carbon\Carbon::parse($data->waktu)->format('d F Y');
        $data->shift = \Carbon\Carbon::parse($data->waktu)->format('H:i'); 
    }

    return view('histori.show', compact('data'));
}

    // Export Laporan Bulanan (Landscape - Tabel Banyak Alat)
    public function export(Request $request)
    {
        $query = HistoriOperasional::with(['alat.subKategori.kategori', 'user']);
    
        $bulan = now()->translatedFormat('F');
        $tahun = now()->format('Y');
    
        if ($request->filled('periode')) {
            try {
                $range = explode(' - ', $request->periode);
                $start = Carbon::createFromFormat('d/m/Y', trim($range[0]))->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', trim($range[1]))->endOfDay();
                $query->whereBetween('waktu', [$start, $end]);
                $bulan = $start->translatedFormat('F');
                $tahun = $start->format('Y');
            } catch (\Exception $e) {}
        }
    
        $query->when($request->jenis_aktivitas, function ($q) use ($request) {
            return $q->where('jenis_aktivitas', $request->jenis_aktivitas);
        })->when($request->kategori, function ($q) use ($request) {
            return $q->whereHas('alat.subKategori.kategori', function($queryKategori) use ($request) {
                $queryKategori->where('nama_kategori', $request->kategori);
            });
        })->when($request->alat_id, function ($q) use ($request) {
            return $q->where('alat_id', $request->alat_id);
        });
    
        $data = $query->latest('waktu')->get()->unique('alat_id');
    
        $pdf = Pdf::loadView('histori.pdf_monitoring', [
            'data' => $data,
            'bulan' => $bulan,
            'tahun' => $tahun
        ])
        ->setPaper('a4', 'landscape')
    ->setOption('isRemoteEnabled', true)      // Tambahkan ini
    ->setOption('isHtml5ParserEnabled', true)
    ->setOption('chroot', public_path());

        return $pdf->download('Laporan_Monitoring_Peralatan_'.$bulan.'_'.$tahun.'.pdf');
    }

    // Export Riwayat Satu Alat (Portrait - Per Alat)
    public function downloadRiwayat($id, Request $request)
    {
        // Ambil parameter jenis dari URL
        $jenis = $request->query('jenis');
    
        // Cek apakah data berasal dari tabel Pengecekan (Maintenance)
        if ($jenis == 'Maintenance Harian' || $jenis == 'Maintenance Mingguan') {
            $data = \App\Models\Pengecekan::with(['alat.subKategori.kategori', 'user'])->findOrFail($id);
            
            // Map field agar sinkron dengan template PDF Riwayat
            $data->waktu_pelaksanaan = $data->waktu; 
            $data->tanggal_display = $data->tanggal;
            $data->deskripsi_hasil = "Kondisi Akhir: " . $data->kondisi_akhir;
            $data->dokumen = $data->foto_kegiatan;
        } else {
            // Ambil dari tabel HistoriOperasional
            $data = HistoriOperasional::with(['alat.subKategori.kategori', 'user'])->findOrFail($id);
        }
        
        $pdf = Pdf::loadView('histori.pdf_riwayat_alat', compact('data'))
              ->setPaper('a4', 'portrait')
              ->setOption('isRemoteEnabled', true)      // Tambahkan ini
              ->setOption('isHtml5ParserEnabled', true); // Tambahkan ini
        
        return $pdf->stream(
    'Riwayat_Alat_' . ($data->alat?->nama_alat ?? 'Tanpa_Alat') . '.pdf');
    }

    public function downloadSingle($id)
    {
        $data = HistoriOperasional::findOrFail($id);
        
        if (!$data->dokumen) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $path = storage_path('app/public/' . $data->dokumen);
        return response()->download($path);
    }
}