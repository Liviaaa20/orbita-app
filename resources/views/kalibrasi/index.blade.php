@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- ─── Header ─────────────────────────────────────────────────────────── --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="m-0 font-weight-bold" style="font-size:1.5rem; color:#1a1a2e;">
                <i class="fas fa-ruler-combined mr-2" style="color:#003366;"></i>Kalibrasi Alat
            </h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">
                @if($canInput)
                    Manajemen &amp; arsip data kalibrasi peralatan laboratorium
                @else
                    Riwayat kalibrasi peralatan laboratorium
                @endif
            </p>
        </div>
        <div class="col-md-6">
            <ol class="breadcrumb float-md-right bg-transparent m-0 p-0" style="font-size:0.8rem;">
                <li class="breadcrumb-item text-muted">Master Data</li>
                <li class="breadcrumb-item active font-weight-bold" style="color:#003366;">Kalibrasi</li>
            </ol>
        </div>
    </div>

    {{-- ─── Flash Messages ─────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center" style="border-radius:8px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" style="border-radius:8px;">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- FORM INPUT — hanya Admin & Teknisi                                     --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @if($canInput)
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; border-top:4px solid #003366;">
        <div class="card-header border-0 bg-white py-3">
            <h6 class="m-0 font-weight-bold" style="font-size:1rem;">
                <i class="fas fa-plus-circle mr-2" style="color:#003366;"></i>Form Input Data Kalibrasi
            </h6>
        </div>
        <div class="card-body py-4">
            <form id="formKalibrasi" action="{{ route('kalibrasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- INPUT FILE — di luar dropZone supaya tidak ikut terhapus saat innerHTML diganti --}}
                <input type="file" name="sertifikat_pdf" id="sertifikatFile"
                       accept=".pdf,.jpg,.jpeg,.png"
                       style="position:fixed; top:-9999px; left:-9999px; opacity:0;">

                <div class="row">
                    {{-- Kategori --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-semibold mb-2" style="font-size:0.9rem;">
                            Kategori Alat <span class="text-danger">*</span>
                        </label>
                        <select name="kategori_id"
                                class="form-control @error('kategori_id') is-invalid @enderror"
                                style="border-radius:8px; height:46px;" required>
                            <option value="" disabled selected>Pilih Kategori Alat...</option>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kalibrator --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-semibold mb-2" style="font-size:0.9rem;">
                            Institusi Kalibrator <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kalibrator" value="{{ old('kalibrator') }}"
                               class="form-control @error('kalibrator') is-invalid @enderror"
                               style="border-radius:8px; height:46px;"
                               placeholder="Contoh: BMKG Pusat, BSN" required>
                        @error('kalibrator')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mt-2">
                    {{-- Tanggal Mulai --}}
                    <div class="col-md-3 form-group">
                        <label class="font-weight-semibold mb-2" style="font-size:0.9rem;">
                            Tanggal Mulai <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                               class="form-control @error('tanggal_mulai') is-invalid @enderror"
                               style="border-radius:8px; height:46px;" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="col-md-3 form-group">
                        <label class="font-weight-semibold mb-2" style="font-size:0.9rem;">
                            Tanggal Selesai <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                               class="form-control @error('tanggal_selesai') is-invalid @enderror"
                               style="border-radius:8px; height:46px;" required>
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nilai Koreksi --}}
                    <div class="col-md-3 form-group">
                        <label class="font-weight-semibold mb-2" style="font-size:0.9rem;">
                            Nilai Koreksi <span class="text-muted font-weight-normal">(Opsional)</span>
                        </label>
                        <input type="number" step="0.0001" name="nilai_koreksi" value="{{ old('nilai_koreksi') }}"
                               class="form-control" style="border-radius:8px; height:46px;" placeholder="0.0000">
                    </div>

                    {{-- Nilai Ketidakpastian --}}
                    <div class="col-md-3 form-group">
                        <label class="font-weight-semibold mb-2" style="font-size:0.9rem;">
                            Ketidakpastian <span class="text-muted font-weight-normal">(Opsional)</span>
                        </label>
                        <input type="number" step="0.0001" name="nilai_ketidakpastian" value="{{ old('nilai_ketidakpastian') }}"
                               class="form-control" style="border-radius:8px; height:46px;" placeholder="0.0000">
                    </div>
                </div>

                <div class="row mt-2">
                    {{-- Upload Sertifikat --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-semibold mb-2" style="font-size:0.9rem;">
                            Sertifikat Kalibrasi
                            <span class="text-muted font-weight-normal">(PDF / JPG / PNG, maks 10 MB)</span>
                        </label>

                        {{-- Drop Zone (UI saja, bukan input) --}}
                        <div id="dropZone"
                             style="border:2px dashed #003366; border-radius:8px; padding:24px;
                                    text-align:center; cursor:pointer; background:#f8fbff; transition:background .2s;">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color:#003366;"></i>
                            <p class="mb-1 font-weight-semibold text-dark" style="font-size:0.85rem;">
                                Klik atau seret berkas ke sini
                            </p>
                            <small class="text-muted">PDF, JPG, PNG — maks 10 MB</small>
                        </div>

                        {{-- Preview Container --}}
                        <div id="previewContainer" class="mt-3 d-none">
                            <div class="card border-0 shadow-sm" style="border-radius:8px; overflow:hidden;">
                                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                    <small class="font-weight-semibold text-dark">
                                        <i class="fas fa-eye mr-1"></i>Pratinjau
                                    </small>
                                    <div>
                                        <button type="button" id="btnPerbesar"
                                                class="btn btn-sm btn-outline-secondary py-0 px-2 mr-1"
                                                title="Lihat ukuran penuh">
                                            <i class="fas fa-expand-alt" style="font-size:0.7rem;"></i> Perbesar
                                        </button>
                                        <button type="button" id="hapusPreview"
                                                class="btn btn-sm btn-outline-danger py-0 px-2"
                                                title="Hapus file">
                                            <i class="fas fa-times"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2 bg-dark text-center"
                                     style="min-height:160px; max-height:260px; overflow:hidden; position:relative;">
                                    {{-- Preview Gambar --}}
                                    <img id="previewImage" src="" alt="Preview"
                                         class="img-fluid d-none"
                                         style="max-height:240px; object-fit:contain; cursor:zoom-in;
                                                border-radius:4px;">
                                    {{-- Preview PDF --}}
                                    <iframe id="previewPdf" src="" class="d-none"
                                            style="width:100%; height:240px; border:none; border-radius:4px;"></iframe>
                                    {{-- Fallback icon PDF (jika iframe diblokir browser) --}}
                                    <div id="previewIcon" class="d-none py-4">
                                        <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                        <p class="mb-0 mt-2 text-white" id="previewIconText" style="font-size:0.8rem;"></p>
                                    </div>
                                </div>
                                <div class="card-footer py-2 px-3 bg-white border-0">
                                    <small class="text-muted" id="fileInfo"></small>
                                </div>
                            </div>
                        </div>

                        @error('sertifikat_pdf')
                            <div class="text-danger mt-1" style="font-size:0.8rem;">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Petugas --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-semibold mb-2" style="font-size:0.9rem;">
                            Petugas Internal <span class="text-muted font-weight-normal">(Opsional)</span>
                        </label>
                        <input type="text" name="petugas" value="{{ old('petugas') }}"
                               class="form-control" style="border-radius:8px; height:46px;"
                               placeholder="Nama Petugas">

                        <div class="mt-3 p-3 rounded" style="background:#e8f4fd; border-left:3px solid #003366;">
                            <small class="text-dark">
                                <i class="fas fa-info-circle mr-1" style="color:#003366;"></i>
                                <strong>Catatan:</strong> Data kalibrasi yang disimpan akan otomatis
                                tercatat pada histori operasional alat.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="button" id="btnReset"
                            class="btn btn-outline-secondary mr-3 px-4 font-weight-semibold"
                            style="border-radius:8px;">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit"
                            class="btn text-white px-4 font-weight-semibold shadow-sm"
                            style="border-radius:8px; background:linear-gradient(135deg,#003366,#004d99); border:none;">
                        <i class="fas fa-save mr-2"></i>Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    {{-- /FORM INPUT --}}


    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- TABEL RIWAYAT — semua role yang canView                                --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-header border-0 bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <h6 class="m-0 font-weight-bold" style="font-size:1rem;">
                        <i class="fas fa-archive mr-2" style="color:#003366;"></i>Daftar Riwayat Kalibrasi
                    </h6>
                    <p class="text-muted mb-0" style="font-size:0.8rem;">Arsip data kalibrasi per kategori alat</p>
                </div>
                <div class="col-md-7">
                    <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                        {{-- Filter Kategori --}}
                        <div class="input-group input-group-sm" style="width:185px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border">
                                    <i class="fas fa-tags text-muted"></i>
                                </span>
                            </div>
                            <select id="filterKategori" class="form-control border">
                                <option value="">Semua Kategori</option>
                                @foreach($kategoris as $k)
                                    <option value="{{ strtolower($k->nama_kategori) }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Filter Bulan --}}
                        <div class="input-group input-group-sm" style="width:160px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border">
                                    <i class="fas fa-calendar text-muted"></i>
                                </span>
                            </div>
                            <input type="month" id="filterBulan" class="form-control border" title="Pilih Periode">
                        </div>
                        {{-- Search --}}
                        <div class="input-group input-group-sm" style="width:185px;">
                            <input type="text" id="tableSearch" class="form-control border" placeholder="Pencarian...">
                            <div class="input-group-append">
                                <span class="input-group-text bg-light border">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                        </div>
                        {{-- Reset --}}
                        <button id="resetFilter" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;" title="Reset">
                            <i class="fas fa-redo-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.875rem;">
                    <thead class="bg-light text-dark font-weight-semibold border-bottom">
                        <tr>
                            <th class="border-0 py-3 pl-4" style="width:50px;">No</th>
                            <th class="border-0 py-3">Kategori</th>
                            <th class="border-0 py-3">Periode</th>
                            <th class="border-0 py-3">Kalibrator</th>
                            <th class="border-0 py-3 text-right">Koreksi</th>
                            <th class="border-0 py-3 text-right">Ketidakpastian</th>
                            <th class="border-0 py-3">Petugas</th>
                            <th class="border-0 py-3 text-center pr-4" style="width:110px;">Berkas</th>
                        </tr>
                    </thead>
                    <tbody id="kalibrasiBody">
                        @forelse($kalibrasis as $data)
                        @php
                            $katNama = strtolower($data->kategori->nama_kategori ?? '');
                            $bulan   = \Carbon\Carbon::parse($data->tanggal_mulai)->format('Y-m');
                            $cari    = strtolower(($data->kategori->nama_kategori ?? '') . ' ' . $data->kalibrator . ' ' . ($data->petugas ?? ''));
                            $ext     = $data->sertifikat_pdf
                                        ? strtolower(pathinfo($data->sertifikat_pdf, PATHINFO_EXTENSION))
                                        : '';
                            $isImage = in_array($ext, ['jpg','jpeg','png']);
                        @endphp
                        <tr class="filter-row"
                            data-kategori="{{ $katNama }}"
                            data-bulan="{{ $bulan }}"
                            data-cari="{{ $cari }}">

                            <td class="py-3 pl-4 text-muted font-weight-bold">{{ $loop->iteration }}</td>

                            <td class="py-3">
                                <span class="badge font-weight-medium px-3 py-2"
                                      style="background:#e3f2fd; color:#003366; font-size:0.75rem;">
                                    {{ $data->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>

                            <td class="py-3 text-muted">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ \Carbon\Carbon::parse($data->tanggal_mulai)->format('d/m/Y') }}
                                &ndash;
                                {{ \Carbon\Carbon::parse($data->tanggal_selesai)->format('d/m/Y') }}
                            </td>

                            <td class="py-3 font-weight-semibold">{{ $data->kalibrator }}</td>

                            <td class="py-3 text-right font-weight-semibold" style="color:#003366;">
                                {{ $data->nilai_koreksi !== null ? number_format($data->nilai_koreksi, 4) : '-' }}
                            </td>

                            <td class="py-3 text-right text-muted">
                                {{ $data->nilai_ketidakpastian !== null ? number_format($data->nilai_ketidakpastian, 4) : '-' }}
                            </td>

                            <td class="py-3">
                                @if($data->petugas)
                                    <span class="badge badge-light border text-dark font-weight-medium">
                                        <i class="fas fa-user mr-1"></i>{{ $data->petugas }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="py-3 text-center pr-4">
                                @if($data->sertifikat_pdf)
                                    {{-- Tombol buka di tab baru --}}
                                    <a href="{{ route('kalibrasi.sertifikat_view', $data->id) }}"
                                       target="_blank"
                                       class="btn btn-sm font-weight-semibold px-2 mb-1 d-block"
                                       style="border-radius:6px; background:#003366; color:#fff; font-size:0.75rem;"
                                       title="Buka di tab baru">
                                        <i class="fas {{ $isImage ? 'fa-image' : 'fa-file-pdf' }} mr-1"></i>
                                        {{ $isImage ? 'Gambar' : 'PDF' }}
                                    </a>
                                    {{-- Tombol pratinjau modal --}}
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary px-2 d-block w-100 btn-preview-tabel"
                                            style="border-radius:6px; font-size:0.72rem;"
                                            data-url="{{ route('kalibrasi.sertifikat_view', $data->id) }}"
                                            data-ext="{{ $ext }}"
                                            title="Pratinjau">
                                        <i class="fas fa-eye mr-1"></i>Preview
                                    </button>
                                @else
                                    <span class="text-muted" style="font-size:0.75rem;">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-3 d-block" style="opacity:.3;"></i>
                                <span class="font-weight-medium">Belum ada riwayat kalibrasi</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-light py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted font-weight-medium">
                    <i class="fas fa-database mr-1"></i>
                    Total Data: <span id="totalData">{{ $kalibrasis->count() }}</span> records
                </small>
                <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i>Data menunjukkan periode kalibrasi per kategori alat
                </small>
            </div>
        </div>
    </div>

</div>{{-- /container-fluid --}}


{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL LIGHTBOX PRATINJAU                                                   --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header py-2 px-4" style="background:#003366;">
                <h6 class="modal-title text-white font-weight-semibold mb-0" id="modalPreviewTitle">
                    <i class="fas fa-file-alt mr-2"></i>Pratinjau Sertifikat
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity:1; font-size:1.2rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-0 bg-dark text-center" style="min-height:60vh;">
                <img id="modalPreviewImg" src="" alt="Preview"
                     class="d-none img-fluid"
                     style="max-height:85vh; object-fit:contain;">
                <iframe id="modalPreviewIframe" src="" class="d-none"
                        style="width:100%; height:85vh; border:none;"></iframe>
                <div id="modalLoading" class="py-5 text-white d-none">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>
                    <small>Memuat berkas...</small>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- SCRIPTS                                                                    --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── 1. FILTER & SEARCH ──────────────────────────────────────────────────
    var filterKategori = document.getElementById('filterKategori');
    var filterBulan    = document.getElementById('filterBulan');
    var tableSearch    = document.getElementById('tableSearch');
    var resetFilter    = document.getElementById('resetFilter');
    var rows           = document.querySelectorAll('.filter-row');
    var totalDataSpan  = document.getElementById('totalData');

    function applyFilters() {
        var kat  = filterKategori ? filterKategori.value.toLowerCase() : '';
        var bln  = filterBulan    ? filterBulan.value : '';
        var cari = tableSearch    ? tableSearch.value.toLowerCase().trim() : '';
        var count = 0;
        rows.forEach(function (row) {
            var show =
                (!kat  || row.dataset.kategori === kat) &&
                (!bln  || row.dataset.bulan    === bln) &&
                (!cari || row.dataset.cari.includes(cari));
            row.style.display = show ? '' : 'none';
            if (show) count++;
        });
        if (totalDataSpan) totalDataSpan.textContent = count;
    }

    if (filterKategori) filterKategori.addEventListener('change', applyFilters);
    if (filterBulan)    filterBulan.addEventListener('change', applyFilters);
    if (tableSearch)    tableSearch.addEventListener('input', applyFilters);
    if (resetFilter)    resetFilter.addEventListener('click', function () {
        if (filterKategori) filterKategori.value = '';
        if (filterBulan)    filterBulan.value    = '';
        if (tableSearch)    tableSearch.value    = '';
        applyFilters();
    });


    // ── 2. MODAL LIGHTBOX ───────────────────────────────────────────────────
    var modalImg     = document.getElementById('modalPreviewImg');
    var modalIframe  = document.getElementById('modalPreviewIframe');
    var modalTitle   = document.getElementById('modalPreviewTitle');
    var modalLoading = document.getElementById('modalLoading');

    function bukaModal(url, ext, judul) {
        // Reset semua elemen
        modalImg.classList.add('d-none');    modalImg.src    = '';
        modalIframe.classList.add('d-none'); modalIframe.src = '';
        if (modalLoading) modalLoading.classList.remove('d-none');

        if (modalTitle) {
            modalTitle.innerHTML = '<i class="fas fa-file-alt mr-2"></i>' + (judul || 'Pratinjau Sertifikat');
        }

        var isImage = ['jpg','jpeg','png'].indexOf(ext.toLowerCase()) !== -1;

        if (isImage) {
            modalImg.onload = function () {
                if (modalLoading) modalLoading.classList.add('d-none');
            };
            modalImg.src = url;
            modalImg.classList.remove('d-none');
        } else {
            if (modalLoading) modalLoading.classList.add('d-none');
            modalIframe.src = url;
            modalIframe.classList.remove('d-none');
        }

        $('#modalPreview').modal('show');
    }

    // Tombol Preview di tabel riwayat
    document.querySelectorAll('.btn-preview-tabel').forEach(function (btn) {
        btn.addEventListener('click', function () {
            bukaModal(this.dataset.url, this.dataset.ext, 'Pratinjau Sertifikat');
        });
    });

    // Bersihkan src saat modal ditutup
    $('#modalPreview').on('hidden.bs.modal', function () {
        modalImg.src    = '';
        modalIframe.src = '';
    });


    // ── 3. UPLOAD PREVIEW (form input) ─────────────────────────────────────
    var dropZone         = document.getElementById('dropZone');
    var fileInput        = document.getElementById('sertifikatFile');
    var previewContainer = document.getElementById('previewContainer');
    var previewImage     = document.getElementById('previewImage');
    var previewPdf       = document.getElementById('previewPdf');
    var previewIcon      = document.getElementById('previewIcon');
    var previewIconText  = document.getElementById('previewIconText');
    var fileInfoEl       = document.getElementById('fileInfo');
    var hapusBtn         = document.getElementById('hapusPreview');
    var perbesarBtn      = document.getElementById('btnPerbesar');
    var resetBtn         = document.getElementById('btnReset');

    if (!dropZone || !fileInput) return; // halaman view-only tidak punya form

    var currentObjectURL = null;

    // Klik drop zone → buka file dialog
    dropZone.addEventListener('click', function () {
        fileInput.click();
    });

    // Drag & drop
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        this.style.background = '#dceeff';
        this.style.borderColor = '#0066cc';
    });
    dropZone.addEventListener('dragleave', function () {
        this.style.background = '#f8fbff';
        this.style.borderColor = '#003366';
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        this.style.background = '#f8fbff';
        this.style.borderColor = '#003366';
        if (e.dataTransfer.files.length) {
            try {
                var dt = new DataTransfer();
                dt.items.add(e.dataTransfer.files[0]);
                fileInput.files = dt.files;
            } catch(err) { /* Safari fallback */ }
            handleFile(e.dataTransfer.files[0]);
        }
    });

    // Perubahan file input
    fileInput.addEventListener('change', function () {
        if (this.files && this.files.length) {
            handleFile(this.files[0]);
        }
    });

    function handleFile(file) {
        var maxSize = 10 * 1024 * 1024; // 10 MB
        var allowed = ['application/pdf', 'image/jpeg', 'image/png'];

        if (file.size > maxSize) {
            alert('Ukuran file melebihi batas 10 MB.\nUkuran file: ' + (file.size/(1024*1024)).toFixed(2) + ' MB');
            clearPreview();
            return;
        }
        if (allowed.indexOf(file.type) === -1) {
            alert('Format tidak didukung.\nGunakan PDF, JPG, atau PNG.');
            clearPreview();
            return;
        }

        // Tampilkan preview
        previewContainer.classList.remove('d-none');
        previewImage.classList.add('d-none');
        previewPdf.classList.add('d-none');
        if (previewIcon) previewIcon.classList.add('d-none');

        var sizeMB = (file.size / (1024 * 1024)).toFixed(2);
        if (fileInfoEl) fileInfoEl.textContent = file.name + '  •  ' + sizeMB + ' MB';

        // Buat Object URL (lebih efisien dari base64)
        if (currentObjectURL) URL.revokeObjectURL(currentObjectURL);
        currentObjectURL = URL.createObjectURL(file);

        if (file.type === 'application/pdf') {
            previewPdf.src = currentObjectURL;
            previewPdf.classList.remove('d-none');
            // Beberapa browser blokir blob PDF di iframe; fallback ke icon
            previewPdf.onerror = function () {
                previewPdf.classList.add('d-none');
                if (previewIcon) {
                    previewIcon.classList.remove('d-none');
                    if (previewIconText) previewIconText.textContent = file.name;
                }
            };
        } else {
            previewImage.src = currentObjectURL;
            previewImage.classList.remove('d-none');
        }

        // Update drop zone UI
        dropZone.style.borderColor = '#28a745';
        dropZone.style.background  = '#f0fff4';
        dropZone.innerHTML =
            '<i class="fas fa-check-circle fa-2x mb-2" style="color:#28a745;"></i>' +
            '<p class="mb-0 font-weight-semibold" style="font-size:0.85rem; color:#28a745;">' +
                escHtml(file.name) +
            '</p>' +
            '<small class="text-muted">Klik untuk ganti berkas</small>';

        // Tombol Perbesar
        if (perbesarBtn) {
            perbesarBtn.disabled = false;
            perbesarBtn.onclick = function (e) {
                e.stopPropagation();
                var ext = file.type === 'application/pdf' ? 'pdf' : 'jpg';
                bukaModal(currentObjectURL, ext, file.name);
            };
        }

        // Klik gambar langsung → perbesar
        previewImage.onclick = function () {
            if (currentObjectURL) bukaModal(currentObjectURL, 'jpg', file.name);
        };
    }

    function clearPreview() {
        if (currentObjectURL) {
            URL.revokeObjectURL(currentObjectURL);
            currentObjectURL = null;
        }
        fileInput.value = '';
        previewContainer.classList.add('d-none');
        previewImage.classList.add('d-none');
        previewImage.src = '';
        previewImage.onclick = null;
        previewPdf.classList.add('d-none');
        previewPdf.src = '';
        if (previewIcon) previewIcon.classList.add('d-none');
        if (fileInfoEl)  fileInfoEl.textContent = '';
        if (perbesarBtn) { perbesarBtn.disabled = true; perbesarBtn.onclick = null; }

        dropZone.style.borderColor = '#003366';
        dropZone.style.background  = '#f8fbff';
        dropZone.innerHTML =
            '<i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color:#003366;"></i>' +
            '<p class="mb-1 font-weight-semibold text-dark" style="font-size:0.85rem;">' +
                'Klik atau seret berkas ke sini' +
            '</p>' +
            '<small class="text-muted">PDF, JPG, PNG — maks 10 MB</small>';
    }

    if (hapusBtn) hapusBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        clearPreview();
    });

    if (resetBtn) resetBtn.addEventListener('click', function () {
        clearPreview();
        document.getElementById('formKalibrasi').reset();
    });

    // Nonaktifkan tombol perbesar saat awal (belum ada file)
    if (perbesarBtn) perbesarBtn.disabled = true;
});

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endsection