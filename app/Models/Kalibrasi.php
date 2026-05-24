<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kalibrasi extends Model
{
    protected $fillable = [
        'alat_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'kalibrator',
        'nilai_koreksi',
        'nilai_ketidakpastian',
        'sertifikat_pdf',
        'petugas',
    ];

    /**
     * Relasi Eloquent ke model Alat
     */
    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}
