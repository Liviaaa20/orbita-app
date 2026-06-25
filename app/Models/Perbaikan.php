<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perbaikan extends Model
{
    use HasFactory;

    protected $table = 'perbaikans';

    protected $fillable = [
        'no_tiket',
        'alat_id',

        // Foto
        'foto_awal',
        'foto_selesai',

        // Tanggal
        'tgl_permintaan',
        'tgl_diterima',
        'tgl_selesai',
        'tgl_validasi',
        'tgl_verifikasi',

        // User
        'user',

        // Informasi Perbaikan
        'kategori_perbaikan',
        'keterangan',
        'catatan',
        'catatan_teknisi',

        // Approval
        'validasi_koordinator',
        'verifikasi_selesai',

        // Status
        'status',
    ];

    protected $casts = [
        'tgl_permintaan' => 'datetime',
        'tgl_diterima'   => 'datetime',
        'tgl_selesai'    => 'datetime',
        'tgl_validasi'   => 'datetime',
        'tgl_verifikasi' => 'datetime',

        'validasi_koordinator' => 'boolean',
        'verifikasi_selesai'   => 'boolean',
    ];

    /*
    |----------------------------------------------------------------------
    | RELATIONSHIP
    |----------------------------------------------------------------------
    */

    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }

    /*
    |----------------------------------------------------------------------
    | ACCESSOR STATUS LABEL (FIXED + COMPLETE)
    |----------------------------------------------------------------------
    */

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {

            // awal laporan
            'pending' => 'Menunggu Validasi',

            // admin / teknisi
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',

            // proses teknisi
            'onproses' => 'Sedang Dikerjakan',

            // tahap akhir sebelum koordinator
            'menunggu_verifikasi' => 'Menunggu Verifikasi Koordinator',

            // selesai final
            'selesai' => 'Selesai',

            default => ucfirst($this->status),
        };
    }

    /*
    |----------------------------------------------------------------------
    | OPTIONAL: HELPER STATUS CHECK (biar enak di blade nanti)
    |----------------------------------------------------------------------
    */

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isDisetujui()
    {
        return $this->status === 'disetujui';
    }

    public function isDitolak()
    {
        return $this->status === 'ditolak';
    }

    public function isOnProses()
    {
        return $this->status === 'onproses';
    }

    public function isSelesai()
    {
        return $this->status === 'selesai';
    }
}