<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Perbaikan extends Model
{
    use HasFactory;

    // Nama tabel di database (pastikan sesuai)
    protected $table = 'perbaikans';

    // TAMBAHKAN KOLOM-KOLOM INI
    protected $fillable = [
        'no_tiket',
        'tgl_permintaan',
        'tgl_diterima',
        'tgl_selesai',
        'user',
        'kategori_perbaikan',
        'keterangan',
        'foto',
        'validasi',
        'alat_id', // WAJIB ADA
        'catatan',
        'status',
    ];
    public function alat()
{
    return $this->belongsTo(Alat::class);
}
}
