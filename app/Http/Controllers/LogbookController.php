<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Pengecekan;
use App\Models\Alat;
use App\Models\SubKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LogbookController extends Controller
{
    // ============================================================
    // HELPER: Cek role user yang sedang login
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
        $subKategoris     = SubKategori::with('kategori')->orderBy('nama_sub_kategori')->get();
        $opsiJenisLogbook = Logbook::pluck('jenis_logbook')->unique()->filter()->values();
        $opsiJenisAlat    = Logbook::pluck('jenis_alat')->unique()->filter()->values();
        $opsiLokasi       = Logbook::pluck('lokasi_tempat')->unique()->filter()->values();

        $query = Logbook::with(['subKategori', 'approvedKanitOleh', 'approvedKoordinatorOleh']);

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
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logbooks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('logbook.index', compact(
            'logbooks',
            'subKategoris',
            'opsiJenisLogbook',
            'opsiJenisAlat',
            'opsiLokasi'
        ));
    }

    // ============================================================
    // SHOW
    // ============================================================
    public function show(Request $request, $id)
    {
        $logbook = Logbook::with([
            'subKategori',
            'approvedKanitOleh',
            'approvedKoordinatorOleh',
        ])->findOrFail($id);

        $alats = collect();
        if ($logbook->sub_kategori_id) {
            $alats = Alat::where('sub_kategori_id', $logbook->sub_kategori_id)
                         ->orderBy('nama_alat')
                         ->get();
        }

        $bulanParam  = $request->get('bulan', now()->format('Y-m'));
        $bulanCarbon = Carbon::createFromFormat('Y-m', $bulanParam);
        $awalBulan   = $bulanCarbon->copy()->startOfMonth();
        $akhirBulan  = $bulanCarbon->copy()->endOfMonth();
        $jumlahHari  = $bulanCarbon->daysInMonth;

        $alatIds = $alats->pluck('id')->toArray();

        $semuaPengecekan = Pengecekan::with(['alat', 'user'])
            ->whereIn('alat_id', $alatIds)
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
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

            if (isset($dataHarian[$hari])) {
                $dataHarian[$hari][$alatId] = $cek->kondisi_akhir;
            }
            if (!empty($cek->catatan)) {
                $keteranganHarian[$hari] = $cek->catatan;
            }
            if ($cek->user) {
                $teknisiHarian[$hari] = $cek->user->name;
            }
        }

        $jumlahTerisi = collect($dataHarian)->filter(fn($d) => !empty($d))->count();

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
            'alats',
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
    // STORE
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'sub_kategori_id'  => 'required|exists:sub_kategoris,id',
            'jenis_logbook'    => 'required|string|max:255',
            'jenis_alat'       => 'required|string|max:255',
            'lokasi_tempat'    => 'required|string|max:255',
            'periode_tersedia' => 'required|string|max:255',
        ]);

        Logbook::create([
            'sub_kategori_id'     => $request->sub_kategori_id,
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
            'sub_kategori_id'  => 'required|exists:sub_kategoris,id',
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
            'sub_kategori_id'     => $request->sub_kategori_id,
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
    // SUBMIT — Admin ajukan ke Kanit
    // ============================================================
    public function submit($id)
    {
        $logbook = Logbook::findOrFail($id);

        if (!$logbook->bisaSubmit()) {
            return redirect()->back()
                ->with('error', 'Logbook ini tidak dapat diajukan saat ini.');
        }

        $logbook->update(['status' => 'pending_kanit']);

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

        // Set approved_kanit dulu, lalu naikkan ke pending_koordinator dalam satu update
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

        $request->validate([
            'catatan_kanit' => 'required|string|max:500',
        ]);

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

        $request->validate([
            'catatan_koordinator' => 'required|string|max:500',
        ]);

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
    // DOWNLOAD PDF
    // Hanya tersedia jika status === 'approved_final'
    // ============================================================
    public function downloadPdf(Request $request, $id)
    {
        $logbook = Logbook::with([
            'subKategori.kategori',
            'approvedKanitOleh',
            'approvedKoordinatorOleh',
        ])->findOrFail($id);

        // Guard: hanya approved_final yang boleh download
        if (!$logbook->bisaDownload()) {
            return redirect()
                ->route('logbook.show', $logbook->id)
                ->with('error', 'PDF hanya tersedia untuk logbook yang sudah disetujui final.');
        }

        // ── Tentukan bulan yang akan dicetak ──────────────────────────
        // Prioritas: query string ?bulan=YYYY-MM
        // Fallback : bulan pertama dari periode_tersedia
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

        // ── Alat berdasarkan sub_kategori logbook ─────────────────────
        $alats = collect();
        if ($logbook->sub_kategori_id) {
            $alats = Alat::where('sub_kategori_id', $logbook->sub_kategori_id)
                ->orderBy('nama_alat')
                ->get();
        }

        // ── Data pengecekan harian ────────────────────────────────────
        $alatIds = $alats->pluck('id')->toArray();

        $semuaPengecekan = Pengecekan::with(['alat', 'user'])
            ->whereIn('alat_id', $alatIds)
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
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

            if (isset($dataHarian[$hari])) {
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

        // ── Path absolut gambar paraf (DomPDF wajib public_path) ─────
        $parafKanitPath = public_path('assets/dist/img/TTD/parafKanit.png');
        $parafKoordPath = public_path('assets/dist/img/TTD/parafKoordinator.png');

        // ── Generate PDF ──────────────────────────────────────────────
        $pdf = Pdf::loadView('logbook.pdf_logbook', compact(
            'logbook',
            'bulanCarbon',
            'bulanParam',
            'alats',
            'jumlahHari',
            'jumlahTerisi',
            'dataHarian',
            'keteranganHarian',
            'teknisiHarian',
            'parafKanitPath',
            'parafKoordPath'
        ))
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'DejaVu Sans',
            'dpi'                  => 96,
        ]);

        // ── Nama file PDF ─────────────────────────────────────────────
        $namaFile = sprintf(
            'Logbook_%s_%s.pdf',
            str_replace([' ', '/'], '_', strtoupper($logbook->jenis_logbook)),
            $bulanCarbon->format('M_Y')
        );

        return $pdf->download($namaFile);
    }
}