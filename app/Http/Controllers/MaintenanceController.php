<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    // --- TETAP SESUAI KEINGINANMU ---
    public function indexHarian() {
        return view('maintenance.harian_index');
    }

    public function indexMingguan() {
        return view('maintenance.mingguan_index');
    }

    // --- PERBAIKAN DI SINI (DITAMPILKAN DI MASTER DATA) ---
    public function showPengecekan(Request $request)
    {
        $type    = $request->type ?? 'harian'; 
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $waktu   = $request->waktu; 

        if($type == 'mingguan' && !$waktu) {
            // Gunakan helper date() bawaan PHP saja agar lebih aman dari error Class
            $waktu = date('l', strtotime($tanggal)); 
            // Ubah ke Bahasa Indonesia jika perlu
            $hariMap = [
                'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
            ];
            $waktu = $hariMap[$waktu] ?? $waktu;
        } elseif(!$waktu) {
            $waktu = 'Pagi';
        }

        // ... sisa kode query kategori ...
        $kategoris = Kategori::whereHas('subKategoris.alats', function($query) use ($type) {
            $query->where('jenis', $type);
        })->with(['subKategoris.alats' => function($query) use ($type) {
            $query->where('jenis', $type);
        }])->get();

        return view('MasterData.pengecekan_index', compact('kategoris', 'tanggal', 'waktu', 'type'));
    }

    public function storeInisiasi(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu'   => 'required', 
            'type'    => 'required|in:harian,mingguan',
            'lokasi'  => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $lokasiKategori = $request->input('lokasi');

            if ($lokasiKategori) {
                foreach ($lokasiKategori as $kategori_id => $lokasis) {
                    $alats = \App\Models\Alat::whereIn('lokasi', $lokasis)
                        ->whereHas('subKategori', function($query) use ($kategori_id) {
                            $query->where('kategori_id', $kategori_id);
                        })->get();

                    foreach ($alats as $alat) {
                        Maintenance::updateOrCreate(
                            [
                                'tanggal' => $request->tanggal,
                                'shift'   => $request->waktu,
                                'alat_id' => $alat->id,
                            ],
                            [
                                'jenis_maintenance' => $request->type,
                                'status'            => 'proses', 
                            ]
                        );
                    }
                }
            }

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Inisiasi Berhasil!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function formMaster(Request $request)
    {
        $tanggal    = $request->tanggal;
        $waktu      = $request->waktu;
        $type       = $request->type;
        $kategoriId = $request->kategori_id;

        $queryPending = Maintenance::where('tanggal', $tanggal)
                                   ->where('shift', $waktu) 
                                   ->where('status', 'proses');

        if ($type == 'mingguan') {
            $queryPending->where('jenis_maintenance', 'mingguan');
        } else {
            $queryPending->where('jenis_maintenance', 'harian');
        }

        $alatIdsPending = $queryPending->pluck('alat_id');

        $querySub = \App\Models\SubKategori::with(['kategori', 'alats' => function($query) use ($alatIdsPending) {
                $query->whereIn('id', $alatIdsPending);
            }])
            ->whereHas('alats', function($query) use ($alatIdsPending) {
                $query->whereIn('id', $alatIdsPending);
            });

        if ($type == 'mingguan' && $kategoriId) {
            $querySub->where('kategori_id', $kategoriId);
        }

        $subKategoris = $querySub->get();

        $groupedByKategori = $subKategoris->groupBy(function($sub) {
            return $sub->kategori->nama_kategori ?? 'Tanpa Kategori';
        });

        return view('maintenance.form_master', compact('groupedByKategori', 'tanggal', 'waktu', 'type'));
    }

    public function storeHasilFisik(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu'   => 'required',
            'type'    => 'required',
            'alat'    => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->alat as $alatId => $data) {
                $pathFoto = null;
                if ($request->hasFile("alat.$alatId.foto_kegiatan")) {
                    $file = $request->file("alat.$alatId.foto_kegiatan");
                    $namaFile = time() . '_' . $alatId . '.' . $file->getClientOriginalExtension();
                    $pathFoto = $file->storeAs('uploads/maintenance', $namaFile, 'public');
                }

                Maintenance::where('tanggal', $request->tanggal)
                    ->where('shift', $request->waktu)
                    ->where('alat_id', $alatId)
                    ->update(['status' => 'selesai']);

                DB::table('pengecekans')->insert([
                    'alat_id'       => $alatId,
                    'user_id'       => auth()->id(),
                    'tanggal'       => $request->tanggal,
                    'waktu'         => $request->waktu, 
                    'is_checked'    => true,
                    'kondisi_akhir' => $data['kondisi_fisik'], 
                    'foto_kegiatan' => $pathFoto,
                    'catatan'       => $data['catatan_khusus'] ?? null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Hasil pengecekan berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}