<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_kategori_id',
        'jenis_logbook',
        'jenis_alat',
        'lokasi_tempat',
        'periode_tersedia',
        'jumlah_data',
        'terakhir_diperbarui',
        'status',
        'approved_kanit_by',
        'approved_kanit_at',
        'catatan_kanit',
        'approved_koordinator_by',
        'approved_koordinator_at',
        'catatan_koordinator',
    ];

    protected $casts = [
        'terakhir_diperbarui'    => 'date',
        'approved_kanit_at'      => 'datetime',
        'approved_koordinator_at'=> 'datetime',
    ];

    // ============================================================
    // RELASI
    // ============================================================
    public function subKategori()
    {
        return $this->belongsTo(SubKategori::class, 'sub_kategori_id');
    }

    public function approvedKanitOleh()
    {
        return $this->belongsTo(User::class, 'approved_kanit_by');
    }

    public function approvedKoordinatorOleh()
    {
        return $this->belongsTo(User::class, 'approved_koordinator_by');
    }

    // ============================================================
    // HELPER: Ambil semua alat dari sub kategori logbook ini
    // ============================================================
    public function getAlats()
    {
        if (!$this->sub_kategori_id) return collect();
        return Alat::where('sub_kategori_id', $this->sub_kategori_id)->get();
    }

    // ============================================================
    // HELPER STATUS — label teks
    // ============================================================
    public function getLabelStatus(): string
    {
        return match($this->status) {
            'draft'                  => 'Draft',
            'pending_kanit'          => 'Menunggu Kanit',
            'approved_kanit'         => 'Disetujui Kanit',
            'rejected_kanit'         => 'Ditolak Kanit',
            'pending_koordinator'    => 'Menunggu Koordinator',
            'approved_final'         => 'Disetujui Final',
            'rejected_koordinator'   => 'Ditolak Koordinator',
            default                  => strtoupper($this->status),
        };
    }

    // ============================================================
    // HELPER STATUS — warna badge Bootstrap
    // ============================================================
    public function getBadgeStatus(): string
    {
        return match($this->status) {
            'draft'                  => 'secondary',
            'pending_kanit'          => 'warning',
            'approved_kanit'         => 'info',
            'rejected_kanit'         => 'danger',
            'pending_koordinator'    => 'warning',
            'approved_final'         => 'success',
            'rejected_koordinator'   => 'danger',
            default                  => 'light',
        };
    }

    // ============================================================
    // HELPER STATUS — cek apakah bisa didownload PDF
    // ============================================================
    public function bisaDownload(): bool
    {
        return $this->status === 'approved_final';
    }

    // ============================================================
    // HELPER STATUS — cek apakah bisa disubmit ke kanit
    // ============================================================
    public function bisaSubmit(): bool
    {
        return in_array($this->status, ['draft', 'rejected_kanit', 'rejected_koordinator']);
    }

    // ============================================================
    // HELPER: Badge warna untuk kondisi alat
    // ============================================================
    public static function getBadgeKondisi(string $kondisi): string
    {
        return match(strtolower($kondisi)) {
            'baik'         => 'success',
            'rusak ringan' => 'warning',
            'rusak berat'  => 'danger',
            'on'           => 'success',
            'off'          => 'secondary',
            default        => 'light',
        };
    }

    // ============================================================
    // HELPER: Label singkat kondisi alat
    // ============================================================
    public static function getLabelKondisi(string $kondisi): string
    {
        return match(strtolower($kondisi)) {
            'baik'         => 'BAIK',
            'rusak ringan' => 'RUSAK RINGAN',
            'rusak berat'  => 'RUSAK BERAT',
            'on'           => 'ON',
            'off'          => 'OFF',
            default        => strtoupper($kondisi),
        };
    }
}