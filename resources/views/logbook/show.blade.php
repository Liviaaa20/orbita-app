@extends('layouts.master')

@section('content')
<div class="container-fluid">

    {{-- ── BREADCRUMB & JUDUL ── --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('logbook.index') }}" class="text-muted">
                            <i class="fas fa-book mr-1"></i>Logbook
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-dark font-weight-bold">
                        {{ $logbook->jenis_logbook }}
                    </li>
                </ol>
            </nav>
            <h4 class="font-weight-bold text-dark mb-0">
                {{ strtoupper($logbook->jenis_logbook) }}
            </h4>
            <small class="text-muted">
                {{ $logbook->lokasi_tempat }} &mdash; {{ $bulanCarbon->isoFormat('MMMM YYYY') }}
            </small>
        </div>

        {{-- Tombol Kembali + Cetak --}}
        <div>
            <a href="{{ route('logbook.index') }}" class="btn btn-light border shadow-sm mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-light border shadow-sm">
                <i class="fas fa-print mr-1"></i> Cetak
            </button>
        </div>
    </div>

    {{-- ── INFO CARDS ── --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box shadow-sm border-0 mb-0">
                <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted small">Periode</span>
                    <span class="info-box-number" style="font-size:1rem;">
                        {{ $bulanCarbon->isoFormat('MMMM YYYY') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box shadow-sm border-0 mb-0">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted small">Hari Terisi</span>
                    <span class="info-box-number" style="font-size:1rem;">
                        {{ $jumlahTerisi }} / {{ $jumlahHari }} hari
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box shadow-sm border-0 mb-0">
                <span class="info-box-icon bg-warning"><i class="fas fa-tools"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted small">Jenis Alat</span>
                    <span class="info-box-number" style="font-size:1rem;">
                        {{ $logbook->jenis_alat }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box shadow-sm border-0 mb-0">
                <span class="info-box-icon bg-secondary"><i class="fas fa-map-marker-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted small">Lokasi</span>
                    <span class="info-box-number" style="font-size:0.85rem; line-height:1.3;">
                        {{ $logbook->lokasi_tempat }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FILTER BULAN ── --}}
    <div class="card shadow-sm border-0 rounded-lg mb-3">
        <div class="card-body p-3">
            <form action="{{ route('logbook.show', $logbook->id) }}" method="GET" class="d-flex align-items-end">
                <div class="mr-3">
                    <label class="mb-1 small font-weight-bold text-muted text-uppercase">Pilih Bulan</label>
                    <select name="bulan" class="form-control shadow-none border custom-select" style="min-width:200px;" onchange="this.form.submit()">
                        @foreach($bulanList as $bln)
                            <option value="{{ $bln['value'] }}" {{ $bulanParam == $bln['value'] ? 'selected' : '' }}>
                                {{ $bln['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Tampilkan
                </button>
            </form>
        </div>
    </div>

    {{-- ── TABEL LOGBOOK HARIAN ── --}}
    <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
            <div>
                <h5 class="font-weight-bold m-0 text-dark">
                    {{ strtoupper($logbook->jenis_logbook) }}
                </h5>
                <small class="text-muted">
                    {{ strtoupper($logbook->lokasi_tempat) }} &mdash;
                    BULAN {{ strtoupper($bulanCarbon->isoFormat('MMMM YYYY')) }}
                </small>
            </div>
        </div>

        @if(empty($definisiKolom))
            {{-- Jenis logbook tidak dikenali --}}
            <div class="card-body text-center py-5 text-muted">
                <i class="fas fa-exclamation-triangle mb-3 d-block text-warning" style="font-size:2.5rem;"></i>
                <p>Definisi kolom untuk jenis logbook <strong>"{{ $logbook->jenis_logbook }}"</strong> belum dikonfigurasi.</p>
                <small>Tambahkan di <code>Logbook::getDefinisiKolom()</code></small>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center mb-0"
                       style="font-size: 0.8rem; min-width: 900px;">

                    {{-- HEADER --}}
                    <thead class="bg-light text-dark font-weight-bold text-uppercase" style="font-size:0.75rem;">
                        <tr>
                            <th class="align-middle" style="width:40px;">TGL</th>
                            @foreach($definisiKolom as $key => $label)
                                <th class="align-middle px-2">{{ strtoupper($label) }}</th>
                            @endforeach
                            <th class="align-middle text-left px-3" style="min-width:180px;">KETERANGAN</th>
                            <th class="align-middle" style="min-width:100px;">TEKNISI</th>
                        </tr>
                    </thead>

                    {{-- BODY: 1 baris per hari --}}
                    <tbody>
                        @for($hari = 1; $hari <= $jumlahHari; $hari++)
                            @php
                                $dataHari = $dataHarian[$hari] ?? [];
                                $adaData  = !empty($dataHari);
                            @endphp
                            <tr class="{{ !$adaData ? 'table-light text-muted' : '' }}">

                                {{-- Kolom Tanggal --}}
                                <td class="font-weight-bold align-middle">{{ $hari }}</td>

                                {{-- Kolom per Alat --}}
                                @foreach($definisiKolom as $namaAlat => $label)
                                    @php
                                        $kondisi = $dataHari[$namaAlat] ?? null;
                                    @endphp
                                    <td class="align-middle">
                                        @if($kondisi)
                                            @php
                                                $badge = Logbook::getBadgeKondisi($kondisi);
                                                $labelKondisi = Logbook::getLabelKondisi($kondisi);
                                            @endphp
                                            <span class="badge badge-{{ $badge }} px-2 py-1">
                                                {{ $labelKondisi }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size:0.75rem;">#N/A</span>
                                        @endif
                                    </td>
                                @endforeach

                                {{-- Kolom Keterangan --}}
                                <td class="text-left px-3 align-middle" style="font-size:0.78rem;">
                                    {{ $keteranganHarian[$hari] ?? '' }}
                                </td>

                                {{-- Kolom Teknisi --}}
                                <td class="align-middle">
                                    @if(!empty($teknisiHarian[$hari]))
                                        <span class="badge badge-light border text-dark px-2">
                                            {{ $teknisiHarian[$hari] }}
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            {{-- LEGENDA --}}
            <div class="card-footer bg-white border-top p-3">
                <small class="text-muted font-weight-bold mr-3">KETERANGAN STATUS:</small>
                <span class="badge badge-success px-2 py-1 mr-2">BAIK</span>
                <span class="badge badge-danger px-2 py-1 mr-2">RUSAK</span>
                <span class="badge badge-warning px-2 py-1 mr-2">RUSAK RINGAN</span>
                <span class="badge badge-secondary px-2 py-1 mr-2">OFF</span>
                <span class="text-muted mr-2" style="font-size:0.8rem;">#N/A = Tidak ada data</span>
            </div>
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, .breadcrumb, .info-box, form, .card-footer { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .card-header { border-bottom: 2px solid #000 !important; }
        table { font-size: 9pt !important; }
        th, td { padding: 4px 6px !important; }
    }
</style>
@endpush