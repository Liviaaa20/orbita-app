@extends('layouts.master')

@section('content')
<div class="container-fluid px-3">
    
    {{-- Header Halaman --}}
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.5rem;">Kalibrasi Alat</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent m-0 p-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item text-muted">Master Data</li>
                <li class="breadcrumb-item active text-dark font-weight-bold">Kalibrasi</li>
            </ol>
        </div>
    </div>

    {{-- Notifikasi Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 1. FORM INPUT DATA (Premium Minimalist Style) --}}
    <div class="card card-default border-0 shadow-sm mb-4" style="border-radius: 8px;">
        <div class="card-body p-4">
            <form action="{{ route('kalibrasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="text-secondary font-weight-normal mb-1" style="font-size: 0.85rem;">Pilih Alat</label>
                        <select name="alat_id" class="form-control @error('alat_id') is-invalid @enderror" style="border-radius: 6px;" required>
                            <option value="" selected disabled>Pilih Alat...</option>
                            @foreach($alats as $alat)
                                <option value="{{ $alat->id }}">{{ $alat->nama_alat }} ({{ $alat->kode_alat }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="text-secondary font-weight-normal mb-1" style="font-size: 0.85rem;">Kalibrator</label>
                        <input type="text" name="kalibrator" class="form-control" style="border-radius: 6px;" placeholder="Nama instansi/perusahaan kalibrasi" required>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-3 form-group">
                        <label class="text-secondary font-weight-normal mb-1" style="font-size: 0.85rem;">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" style="border-radius: 6px;" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="text-secondary font-weight-normal mb-1" style="font-size: 0.85rem;">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" style="border-radius: 6px;" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="text-secondary font-weight-normal mb-1" style="font-size: 0.85rem;">Nilai Koreksi</label>
                        <input type="number" step="0.01" name="nilai_koreksi" class="form-control" style="border-radius: 6px;" placeholder="0.00" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="text-secondary font-weight-normal mb-1" style="font-size: 0.85rem;">Nilai Ketidakpastian</label>
                        <input type="number" step="0.01" name="nilai_ketidakpastian" class="form-control" style="border-radius: 6px;" placeholder="0.00" required>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6 form-group">
                        <label class="text-secondary font-weight-normal mb-1" style="font-size: 0.85rem;">Sertifikat Kalibrasi (PDF)</label>
                        <input type="file" name="sertifikat_pdf" class="form-control-file p-1 border" style="border-radius: 6px; width: 100%;" accept=".pdf">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format berkas wajib PDF (Maksimal 10MB)</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="text-secondary font-weight-normal mb-1" style="font-size: 0.85rem;">Petugas Internal</label>
                        <input type="text" name="petugas" class="form-control" style="border-radius: 6px;" value="{{ Auth::user()->username ?? '' }}" placeholder="Nama petugas" required>
                    </div>
                </div>

                <div class="d-flex justify-content-start mt-3 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" style="border-radius: 6px; font-size: 0.85rem; background-color: #003366; border-color: #003366;">Simpan Data</button>
                    <button type="reset" class="btn btn-light border px-4 ml-2 text-secondary" style="border-radius: 6px; font-size: 0.85rem;">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. TABEL DATA REKAP (Clean Data-Table) --}}
    <div class="card card-default border-0 shadow-sm" style="border-radius: 8px;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                <div class="mb-2 mb-md-0">
                    <span class="text-muted font-weight-bold" style="font-size: 0.9rem;">Daftar Riwayat Kalibrasi</span>
                </div>
                <div>
                    <input type="text" id="tableSearch" class="form-control form-control-sm px-3" placeholder="Cari data kalibrasi..." style="width: 240px; border-radius: 6px;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0" id="kalibrasiTable" style="font-size: 0.85rem;">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="border-top-0 border-bottom-0 py-3">Tanggal Periode</th>
                            <th class="border-top-0 border-bottom-0 py-3">Nama Alat</th>
                            <th class="border-top-0 border-bottom-0 py-3">Kalibrator</th>
                            <th class="border-top-0 border-bottom-0 py-3 text-right">Nilai Koreksi</th>
                            <th class="border-top-0 border-bottom-0 py-3 text-right">Ketidakpastian</th>
                            <th class="border-top-0 border-bottom-0 py-3">Petugas</th>
                            <th class="border-top-0 border-bottom-0 py-3 text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark">
                       @forelse($kalibrasis as $data)
                            <tr>
                                <td class="py-3 text-muted">
                                    {{ \Carbon\Carbon::parse($data->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($data->tanggal_selesai)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 font-weight-bold text-dark">{{ $data->alat->nama_alat ?? '-' }}</td>
                                <td class="py-3 text-secondary">{{ $data->kalibrator }}</td>
                                <td class="py-3 text-right font-weight-bold text-primary">{{ number_format($data->nilai_koreksi, 2) }}</td>
                                <td class="py-3 text-right text-muted font-monospace">{{ number_format($data->nilai_ketidakpastian, 2) }}</td>
                                <td class="py-3"><span class="badge badge-light border text-dark px-2 py-1 font-weight-normal">{{ $data->petugas }}</span></td>
                                <td class="py-3 text-center">
                                    @if($data->sertifikat_pdf)
                                        {{-- Sempurna: Dipastikan menggunakan strtolower agar format .PNG / .JPG kapital tetap terbaca gambar --}}
                                        @php
                                            $ekstensi = pathinfo($data->sertifikat_pdf, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($ekstensi), ['jpg', 'jpeg', 'png']);
                                        @endphp
                                        
                                        <a href="{{ route('kalibrasi.sertifikat_view', $data->id) }}" target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold px-2" style="border-radius: 4px; font-size: 0.75rem;" title="Lihat Berkas">
                                            <i class="fas {{ $isImage ? 'fa-image' : 'fa-file-pdf' }} mr-1"></i> 
                                            {{ $isImage ? 'Gambar' : 'Dokumen' }}
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-light border text-muted" disabled style="cursor: not-allowed; font-size: 0.75rem;">
                                            <i class="fas fa-ban mr-1"></i> Kosong
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Belum ada riwayat data kalibrasi alat yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Fitur Live Filter Pencarian Otomatis Tanpa Reload --}}
<script>
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('#kalibrasiTable tbody tr');
        
        rows.forEach(row => {
            if(row.cells.length > 1) {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            }
        });
    });
</script>
@endsection