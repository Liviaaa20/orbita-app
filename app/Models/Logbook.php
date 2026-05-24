<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_logbook',
        'jenis_alat',
        'lokasi_tempat',
        'periode_tersedia',
        'jumlah_data',
        'terakhir_diperbarui',
    ];

    protected $casts = [
        'terakhir_diperbarui' => 'date',
    ];

    // ============================================================
    // DEFINISI KOLOM PER JENIS LOGBOOK
    // Key   = nama kolom internal (sesuai nama_alat di tabel alats)
    // Value = label yang tampil di header tabel
    // ============================================================
    public static function getDefinisiKolom(string $jenisLogbook): array
    {
        $map = [
            // ── LOG BOOK PERALATAN KONVENSIONAL ──────────────────────────
            'LOG BOOK PERALATAN KONVENSIONAL' => [
                'TERMOMETER (BK)'        => 'Termometer (BK)',
                'TERMOMETER (MAX)'       => 'Termometer (Max)',
                'TERMOMETER (MIN)'       => 'Termometer (Min)',
                'RAIN GAUGE (OBS)'       => 'Rain Gauge (Obs)',
                'RAIN GAUGE (HELLMAN)'   => 'Rain Gauge (Hellman)',
                'CAMPBELL STOKES'        => 'Campbell Stokes',
                'EVAPORIMETER'           => 'Evaporimeter',
                'TERMOHYGROGRAPH'        => 'Termohygrograph',
                'BAROGRAPH'              => 'Barograph',
                'CMSS (LINTAS ARTA)'     => 'CMSS (Lintas Arta)',
            ],

            // ── LOG BOOK AWS DIGITALISASI ────────────────────────────────
            'LOG BOOK AWS DIGITALISASI' => [
                'ANEMOMETER'             => 'Anemometer',
                'TERMOMETER (UDARA)'     => 'Termometer (Udara)',
                'HYGROMETER'             => 'Hygrometer',
                'BAROMETER'              => 'Barometer',
                'RAIN GAUGE'             => 'Rain Gauge',
                'PYRANOMETER'            => 'Pyranometer',
                'TERMOMETER (AIR)'       => 'Termometer (Air)',
                'EVAPORIMETER'           => 'Evaporimeter',
                'PC SERVER'              => 'PC Server',
            ],

            // ── LOG BOOK AWS MARITIM ─────────────────────────────────────
            'LOG BOOK AWS MARITIM' => [
                'ANEMOMETER'             => 'Anemometer',
                'TERMOMETER (UDARA)'     => 'Termometer (Udara)',
                'HYGROMETER'             => 'Hygrometer',
                'BAROMETER'              => 'Barometer',
                'RAIN GAUGE'             => 'Rain Gauge',
                'PYRANOMETER'            => 'Pyranometer',
                'TERMOMETER (AIR)'       => 'Termometer (Air)',
                'WATER LEVEL'            => 'Water Level',
                'CCTV'                   => 'CCTV',
            ],

            // ── LOG BOOK DISPLAY ─────────────────────────────────────────
            'LOG BOOK DISPLAY' => [
                'DISPLAY'                => 'Display',
            ],

            // ── LOG BOOK MAWS/VAWS ───────────────────────────────────────
            'LOG BOOK MAWS/VAWS' => [
                'MAWS'                   => 'MAWS/VAWS',
            ],
        ];

        // Cari berdasarkan partial match (case-insensitive)
        foreach ($map as $key => $kolom) {
            if (stripos($jenisLogbook, $key) !== false || stripos($key, $jenisLogbook) !== false) {
                return $kolom;
            }
        }

        // Fallback: kembalikan kolom kosong jika tidak ditemukan
        return [];
    }

    // ============================================================
    // HELPER: Badge warna untuk status kondisi alat
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
    // HELPER: Label singkat untuk kondisi
    // ============================================================
    public static function getLabelKondisi(string $kondisi): string
    {
        return match(strtolower($kondisi)) {
            'baik'         => 'BAIK',
            'rusak ringan' => 'RUSAK',
            'rusak berat'  => 'RUSAK',
            'on'           => 'ON',
            'off'          => 'OFF',
            default        => strtoupper($kondisi),
        };
    }
}