@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-weight-bold text-dark">
            <a href="{{ route('histori.index') }}" class="btn btn-sm btn-light mr-2"><i class="fas fa-arrow-left"></i></a>
            Detail Hasil Pengecekan Peralatan
        </h4>
        <div class="btn-group shadow-sm">
            <button onclick="window.print()" class="btn btn-light btn-sm border">
                <i class="fas fa-print mr-1"></i> Print Page
            </button>
            {{-- Sesuaikan route download jika ada --}}
            <a href="#" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf mr-1"></i> Cetak Report
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            {{-- KARTU 1: INFORMASI PERALATAN --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white py-3">
                    <h6 class="font-weight-bold m-0 text-navy"><i class="fas fa-info-circle mr-2"></i>Informasi Peralatan</h6>
                </div>
                <div class="card-body">
<table class="table table-sm table-borderless">
    <tr>
        <th width="200">Nama Alat</th>
        <td>:
            <strong class="text-primary">
                {{ optional($data->alat)->nama_alat ?? 'Tanpa Alat' }}
            </strong>
        </td>
    </tr>

    <tr>
        <th>Kategori / Sub</th>
        <td>:
            @if($data->alat && $data->alat->subKategori)
                {{ $data->alat->subKategori->kategori->nama_kategori ?? '-' }}
                /
                {{ $data->alat->subKategori->nama_sub_kategori ?? '-' }}
            @else
                -
            @endif
        </td>
    </tr>

    <tr>
        <th>Nomor Seri (S/N)</th>
        <td>:
            <code class="text-dark">
                {{ optional($data->alat)->nomor_seri ?? '-' }}
            </code>
        </td>
    </tr>

    <tr>
        <th>Lokasi Penempatan</th>
        <td>:
            {{ optional($data->alat)->lokasi ?? '-' }}
        </td>
    </tr>

    <tr class="border-top">
        <th class="pt-3">Tanggal Pengecekan</th>
        <td class="pt-3">
            :
            @if(!empty($data->tanggal))
                {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}
            @elseif(!empty($data->waktu))
                {{ \Carbon\Carbon::parse($data->waktu)->translatedFormat('d F Y') }}
            @else
                -
            @endif
        </td>
    </tr>

    <tr>
        <th>Waktu / Shift</th>
        <td>:
            <span class="badge badge-info">
                {{ $data->waktu_pelaksanaan ?? $data->waktu ?? '-' }}
            </span>
        </td>
    </tr>

    <tr>
        <th>Kondisi Akhir</th>
        <td>:
            @php
                $badge = 'badge-secondary';

                if ($data->kondisi_akhir == 'Baik') {
                    $badge = 'badge-success';
                } elseif ($data->kondisi_akhir == 'Rusak Ringan') {
                    $badge = 'badge-warning';
                } elseif ($data->kondisi_akhir == 'Rusak Berat') {
                    $badge = 'badge-danger';
                }
            @endphp

            <span class="badge {{ $badge }}">
                {{ $data->kondisi_akhir ?? '-' }}
            </span>
        </td>
    </tr>
</table>
                </div>
            </div>

            {{-- KARTU 2: CATATAN & HASIL --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3">
                    <h6 class="font-weight-bold m-0 text-dark">Hasil Pemeriksaan & Catatan</h6>
                </div>
                <div class="card-body">
                    <label class="small font-weight-bold text-muted">CATATAN KHUSUS DARI PETUGAS:</label>
                    <div class="p-3 bg-light rounded border-left" style="border-left: 4px solid #003366 !important;">
                        <p class="mb-0 italic">{{ $data->catatan ?? 'Tidak ada catatan khusus yang diinputkan.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- KARTU 3: PETUGAS --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body">
                    <label class="small font-weight-bold text-muted d-block">PETUGAS PELAKSANA</label>
                    <div class="d-flex align-items-center">
                        <div class="bg-navy text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; background-color: #003366;">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <p class="mb-0 font-weight-bold">{{ $data->user->username ?? 'Petugas' }}</p>
                            <small class="text-muted">ID Petugas: {{ $data->user_id }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KARTU 4: FOTO KEGIATAN (Ini yang kamu upload di storeHasilFisik) --}}
{{-- KARTU 4: DOKUMEN / FOTO --}}
<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-header bg-white py-3 text-center">
        <h6 class="font-weight-bold m-0">Foto Dokumentasi</h6>
    </div>

    <div class="card-body text-center">

        @if($data->dokumen)

            @php
                $ext = strtolower(pathinfo($data->dokumen, PATHINFO_EXTENSION));
                $imgExt = ['jpg', 'jpeg', 'png', 'webp'];
            @endphp

            @if(in_array($ext, $imgExt))

                <img src="{{ asset('storage/' . $data->dokumen) }}"
                     class="img-fluid rounded shadow-sm mb-3"
                     alt="Foto Dokumentasi"
                     style="max-height:250px; object-fit:cover;">

                <a href="{{ asset('storage/' . $data->dokumen) }}"
                   target="_blank"
                   class="btn btn-outline-primary btn-block btn-sm">
                    <i class="fas fa-search-plus mr-1"></i>
                    Lihat Foto Full
                </a>

            @else

                <a href="{{ asset('storage/' . $data->dokumen) }}"
                   target="_blank"
                   class="btn btn-danger btn-block">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Lihat Dokumen
                </a>

            @endif

        @else

            <div class="py-4">
                <i class="fas fa-image fa-4x text-light mb-3"></i>
                <p class="text-muted small">
                    Tidak ada dokumentasi yang diunggah.
                </p>
            </div>

        @endif

    </div>
</div>
        </div>
    </div>
</div>
@endsection