<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Pengecekan;
use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LogbookController extends Controller
{
    // ============================================================
    // HELPER ROLE
    // ============================================================
    private function getRole(): string
    {
        return strtolower(Auth::user()->role->nama_role ?? '');
    }

    private function isAdmin(): bool
    {
        return $this->getRole() === 'admin';
    }

    private function isKanit(): bool
    {
        return in_array($this->getRole(), ['kepala unit', 'kepala_unit', 'kanit']);
    }

    private function isKoordinator(): bool
    {
        return $this->getRole() === 'koordinator';
    }

    // ============================================================
    // INDEX
    // ============================================================
    public function index(Request $request)
    {
        // Dropdown opsi filter
        $kategoris        = Kategori::orderBy('nama_kategori')->get();
        $opsiJenisLogbook = Logbook::pluck('jenis_logbook')->unique()->filter()->values();
        $opsiJenisAlat    = Logbook::pluck('jenis_alat')->unique()->filter()->values();

        $query = Logbook::with([
            'kategori',
            'approvedKanitOleh',
            'approvedKoordinatorOleh',
        ]);

        if ($request->filled('jenis_logbook') && $request->jenis_logbook !== 'Semua Logbook') {
            $query->where('jenis_logbook', $request->jenis_logbook);
        }
        if ($request->filled('jenis_alat') && $request->jenis_alat !== 'Semua Logbook') {
            $query->where('jenis_alat', $request->jenis_alat);
        }
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->filled('search')) {
            $query->where('jenis_logbook', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logbooks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('logbook.index', compact(
            'logbooks',
            'kategoris',
            'opsiJenisLogbook',
            'opsiJenisAlat'
        ));
    }

    // ============================================================
    // SHOW — tarik data dari pengecekans otomatis
    // ============================================================
    public function show(Request $request, $id)
    {
        $logbook = Logbook::with([
            'kategori',
            'approvedKanitOleh',
            'approvedKoordinatorOleh',
        ])->findOrFail($id);

        // Ambil semua alat berdasarkan kategori_id
        $alats = collect();
        if ($logbook->kategori_id) {
            $alats = Alat::whereHas('subKategori', function ($q) use ($logbook) {
                $q->where('kategori_id', $logbook->kategori_id);
            })->orderBy('nama_alat')->get();
        }

        // Filter bulan
        $bulanParam  = $request->get('bulan', now()->format('Y-m'));
        $bulanCarbon = Carbon::createFromFormat('Y-m', $bulanParam);
        $awalBulan   = $bulanCarbon->copy()->startOfMonth();
        $akhirBulan  = $bulanCarbon->copy()->endOfMonth();
        $jumlahHari  = $bulanCarbon->daysInMonth;

        $alatIds = $alats->pluck('id')->toArray();

        // Ambil pengecekan — prioritas shift Pagi dulu
        $semuaPengecekan = Pengecekan::with(['alat', 'user'])
            ->whereIn('alat_id', $alatIds)
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->orderByRaw("FIELD(waktu, 'Pagi', 'Siang', 'Malam')")
            ->get();

        // Susun data per hari
        $dataHarian       = [];
        $keteranganHarian = [];
        $teknisiHarian    = [];

        for ($hari = 1; $hari <= $jumlahHari; $hari++) {
            $dataHarian[$hari]       = [];
            $keteranganHarian[$hari] = '';
            $teknisiHarian[$hari]    = '';
        }

        foreach ($semuaPengecekan as $cek) {
            $hari   = (int) Carbon::parse($cek->tanggal)->format('j');
            $alatId = $cek->alat_id;

            // Tidak overwrite — ambil data shift pertama saja
            if (isset($dataHarian[$hari]) && !isset($dataHarian[$hari][$alatId])) {
                $dataHarian[$hari][$alatId] = $cek->kondisi_akhir;
            }
            if (!empty($cek->catatan) && empty($keteranganHarian[$hari])) {
                $keteranganHarian[$hari] = $cek->catatan;
            }
            if ($cek->user && empty($teknisiHarian[$hari])) {
                $teknisiHarian[$hari] = $cek->user->name;
            }
        }

        $jumlahTerisi = collect($dataHarian)->filter(fn($d) => !empty($d))->count();
        $persenTerisi = $jumlahHari > 0 ? round(($jumlahTerisi / $jumlahHari) * 100) : 0;

        // List bulan untuk dropdown filter (12 bulan ke belakang)
        $bulanList = [];
        for ($i = 0; $i < 12; $i++) {
            $bln = now()->copy()->subMonths($i);
            $bulanList[] = [
                'value' => $bln->format('Y-m'),
                'label' => $bln->isoFormat('MMMM YYYY'),
            ];
        }

        $isAdmin       = $this->isAdmin();
        $isKanit       = $this->isKanit();
        $isKoordinator = $this->isKoordinator();

        return view('logbook.show', compact(
            'logbook',
            'alats',
            'dataHarian',
            'keteranganHarian',
            'teknisiHarian',
            'jumlahHari',
            'jumlahTerisi',
            'persenTerisi',
            'bulanParam',
            'bulanCarbon',
            'bulanList',
            'isAdmin',
            'isKanit',
            'isKoordinator'
        ));
    }

    // ============================================================
    // STORE
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id'      => 'required|exists:kategoris,id',
            'jenis_logbook'    => 'required|string|max:255',
            'jenis_alat'       => 'required|string|max:255',
            'lokasi_tempat'    => 'required|string|max:255',
            'periode_tersedia' => 'required|string|max:255',
        ]);

        Logbook::create([
            'kategori_id'         => $request->kategori_id,
            'jenis_logbook'       => $request->jenis_logbook,
            'jenis_alat'          => $request->jenis_alat,
            'lokasi_tempat'       => $request->lokasi_tempat,
            'periode_tersedia'    => $request->periode_tersedia,
            'jumlah_data'         => 0,
            'terakhir_diperbarui' => now(),
            'status'              => 'draft',
        ]);

        return redirect()->route('logbook.index')
            ->with('success', 'Logbook baru berhasil ditambahkan!');
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_id'      => 'required|exists:kategoris,id',
            'jenis_logbook'    => 'required|string|max:255',
            'jenis_alat'       => 'required|string|max:255',
            'lokasi_tempat'    => 'required|string|max:255',
            'periode_tersedia' => 'required|string|max:255',
        ]);

        $logbook = Logbook::findOrFail($id);

        if (!$logbook->bisaSubmit()) {
            return redirect()->route('logbook.index')
                ->with('error', 'Logbook yang sudah diajukan tidak dapat diedit.');
        }

        $logbook->update([
            'kategori_id'         => $request->kategori_id,
            'jenis_logbook'       => $request->jenis_logbook,
            'jenis_alat'          => $request->jenis_alat,
            'lokasi_tempat'       => $request->lokasi_tempat,
            'periode_tersedia'    => $request->periode_tersedia,
            'terakhir_diperbarui' => now(),
        ]);

        return redirect()->route('logbook.index')
            ->with('success', 'Data logbook berhasil diperbarui!');
    }

    // ============================================================
    // DESTROY
    // ============================================================
    public function destroy($id)
    {
        $logbook = Logbook::findOrFail($id);
        $logbook->delete();

        return redirect()->route('logbook.index')
            ->with('success', 'Data logbook berhasil dihapus!');
    }

    // ============================================================
    // SUBMIT → pending_kanit
    // ============================================================
    public function submit($id)
    {
        $logbook = Logbook::findOrFail($id);

        if (!$logbook->bisaSubmit()) {
            return redirect()->back()
                ->with('error', 'Logbook ini tidak dapat diajukan saat ini.');
        }

        $logbook->update([
            'status'              => 'pending_kanit',
            'terakhir_diperbarui' => now(),
        ]);

        return redirect()->route('logbook.index')
            ->with('success', 'Logbook berhasil diajukan ke Kepala Unit!');
    }

    // ============================================================
    // APPROVE KANIT
    // ============================================================
    public function approveKanit(Request $request, $id)
    {
        if (!$this->isKanit()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $logbook = Logbook::findOrFail($id);

        if ($logbook->status !== 'pending_kanit') {
            return redirect()->back()
                ->with('error', 'Logbook ini tidak dalam status menunggu persetujuan Kanit.');
        }

        $logbook->update([
            'status'            => 'pending_koordinator',
            'approved_kanit_by' => Auth::id(),
            'approved_kanit_at' => now(),
            'catatan_kanit'     => $request->catatan_kanit,
        ]);

        return redirect()->back()
            ->with('success', 'Logbook disetujui dan diteruskan ke Koordinator!');
    }

    // ============================================================
    // REJECT KANIT
    // ============================================================
    public function rejectKanit(Request $request, $id)
    {
        if (!$this->isKanit()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $logbook = Logbook::findOrFail($id);

        if ($logbook->status !== 'pending_kanit') {
            return redirect()->back()
                ->with('error', 'Logbook ini tidak dalam status menunggu persetujuan Kanit.');
        }

        $request->validate(['catatan_kanit' => 'required|string|max:500']);

        $logbook->update([
            'status'            => 'rejected_kanit',
            'approved_kanit_by' => Auth::id(),
            'approved_kanit_at' => now(),
            'catatan_kanit'     => $request->catatan_kanit,
        ]);

        return redirect()->back()
            ->with('success', 'Logbook ditolak. Admin dapat merevisi dan mengajukan ulang.');
    }

    // ============================================================
    // APPROVE KOORDINATOR
    // ============================================================
    public function approveKoordinator(Request $request, $id)
    {
        if (!$this->isKoordinator()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $logbook = Logbook::findOrFail($id);

        if ($logbook->status !== 'pending_koordinator') {
            return redirect()->back()
                ->with('error', 'Logbook ini tidak dalam status menunggu persetujuan Koordinator.');
        }

        $logbook->update([
            'status'                  => 'approved_final',
            'approved_koordinator_by' => Auth::id(),
            'approved_koordinator_at' => now(),
            'catatan_koordinator'     => $request->catatan_koordinator,
        ]);

        return redirect()->back()
            ->with('success', 'Logbook disetujui final! PDF sudah bisa diunduh.');
    }

    // ============================================================
    // REJECT KOORDINATOR
    // ============================================================
    public function rejectKoordinator(Request $request, $id)
    {
        if (!$this->isKoordinator()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $logbook = Logbook::findOrFail($id);

        if ($logbook->status !== 'pending_koordinator') {
            return redirect()->back()
                ->with('error', 'Logbook ini tidak dalam status menunggu persetujuan Koordinator.');
        }

        $request->validate(['catatan_koordinator' => 'required|string|max:500']);

        $logbook->update([
            'status'                  => 'rejected_koordinator',
            'approved_koordinator_by' => Auth::id(),
            'approved_koordinator_at' => now(),
            'catatan_koordinator'     => $request->catatan_koordinator,
        ]);

        return redirect()->back()
            ->with('success', 'Logbook ditolak oleh Koordinator.');
    }

    // ============================================================
    // DOWNLOAD PDF — hanya approved_final
    // ============================================================
    public function downloadPdf(Request $request, $id)
    {
        $logbook = Logbook::with([
            'kategori',
            'approvedKanitOleh',
            'approvedKoordinatorOleh',
        ])->findOrFail($id);

        if (!$logbook->bisaDownload()) {
            return redirect()->route('logbook.show', $logbook->id)
                ->with('error', 'PDF hanya tersedia untuk logbook yang sudah disetujui final.');
        }

        // Tentukan bulan
        $bulanParam = $request->get('bulan');
        if ($bulanParam) {
            try {
                $bulanCarbon = Carbon::createFromFormat('Y-m', $bulanParam)->startOfMonth();
            } catch (\Exception $e) {
                $bulanCarbon = Carbon::now()->startOfMonth();
            }
        } else {
            try {
                $bagian      = explode(' - ', $logbook->periode_tersedia);
                $bulanCarbon = Carbon::parse(trim($bagian[0]))->startOfMonth();
            } catch (\Exception $e) {
                $bulanCarbon = Carbon::now()->startOfMonth();
            }
        }

        $jumlahHari = $bulanCarbon->daysInMonth;
        $awalBulan  = $bulanCarbon->copy()->startOfMonth();
        $akhirBulan = $bulanCarbon->copy()->endOfMonth();

        // Alat dari kategori
        $alats = collect();
        if ($logbook->kategori_id) {
            $alats = Alat::whereHas('subKategori', function ($q) use ($logbook) {
                $q->where('kategori_id', $logbook->kategori_id);
            })->orderBy('nama_alat')->get();
        }

        $alatIds = $alats->pluck('id')->toArray();

        $semuaPengecekan = Pengecekan::with(['alat', 'user'])
            ->whereIn('alat_id', $alatIds)
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->orderByRaw("FIELD(waktu, 'Pagi', 'Siang', 'Malam')")
            ->get();

        $dataHarian       = [];
        $keteranganHarian = [];
        $teknisiHarian    = [];

        for ($hari = 1; $hari <= $jumlahHari; $hari++) {
            $dataHarian[$hari]       = [];
            $keteranganHarian[$hari] = '';
            $teknisiHarian[$hari]    = '';
        }

        foreach ($semuaPengecekan as $cek) {
            $hari   = (int) Carbon::parse($cek->tanggal)->format('j');
            $alatId = $cek->alat_id;

            if (isset($dataHarian[$hari]) && !isset($dataHarian[$hari][$alatId])) {
                $dataHarian[$hari][$alatId] = $cek->kondisi_akhir;
            }
            if (!empty($cek->catatan) && empty($keteranganHarian[$hari])) {
                $keteranganHarian[$hari] = $cek->catatan;
            }
            if ($cek->user && empty($teknisiHarian[$hari])) {
                $teknisiHarian[$hari] = $cek->user->name;
            }
        }

        $jumlahTerisi = collect($dataHarian)->filter(fn($d) => !empty($d))->count();

        $pdf = Pdf::loadView('logbook.pdf_logbook', compact(
            'logbook',
            'bulanCarbon',
            'bulanParam',
            'alats',
            'jumlahHari',
            'jumlahTerisi',
            'dataHarian',
            'keteranganHarian',
            'teknisiHarian'
        ))
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'DejaVu Sans',
            'dpi'                  => 96,
        ]);

        $namaFile = sprintf(
            'Logbook_%s_%s.pdf',
            str_replace([' ', '/'], '_', strtoupper($logbook->jenis_logbook)),
            $bulanCarbon->format('M_Y')
        );

        return $pdf->download($namaFile);
    }
}