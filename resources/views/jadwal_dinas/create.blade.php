@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark" style="font-size: 1.6rem;">Input Jadwal Dinas</h1>
            <p class="text-muted small mb-0 mt-1">Halaman khusus Kepala kelompok untukmenambahkan penugasan tim lapangan.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert">
            <i class="icon fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- SISI KIRI: FORMULIR INPUT JADWAL --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 rounded-lg overflow-hidden h-100" style="border-top: 3px solid #003366;">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold text-dark m-0" style="font-size: 1.1rem;">Formulir Jadwal Baru</h3>
                </div>
                
                <form action="{{ route('jadwal_dinas.store') }}" method="POST" class="d-flex flex-column h-100">
                    @csrf
                    <div class="card-body text-sm flex-grow-1">
                        <div class="form-group mb-3">
                            <label for="nama" class="font-weight-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.7rem;">Nama Petugas</label>
                            <input type="text" name="nama" id="nama" required class="form-control rounded-lg shadow-none border" placeholder="Masukkan nama petugas">
                        </div>

                        <div class="form-group mb-3">
                            <label for="tanggal" class="font-weight-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.7rem;">Tanggal Dinas</label>
                            <input type="date" name="tanggal" id="tanggal" required class="form-control rounded-lg shadow-none border">
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="shift" class="font-weight-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.7rem;">Shift Kerja</label>
                                    <select name="shift" id="shift" required class="form-control rounded-lg shadow-none border bg-white" style="height: calc(2.4rem + 2px);">
                                        <option value="" disabled selected>-- Pilih Shift --</option>
                                        <option value="PS1">PS1</option>
                                        <option value="P">P</option>
                                        <option value="S">S</option>
                                        <option value="PS">PS</option>
                                        <option value="TP1">TP1</option>
                                        <option value="TP">TP</option>
                                        <option value="S2">S2</option>
                                        <option value="R">R</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="jam" class="font-weight-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.7rem;">Alokasi Jam</label>
                                    <input type="text" name="jam" id="jam" placeholder="Otomatis terisi..." required class="form-control rounded-lg shadow-none border bg-light" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light text-right border-0" style="gap: 8px;">
                        <a href="{{ route('jadwal_dinas.index') }}" class="btn btn-default font-weight-medium rounded-lg px-4 border">Kembali</a>
                        <button type="submit" class="btn btn-primary font-weight-bold rounded-lg px-4 shadow-sm" style="background-color: #003366; border-color: #003366;">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SISI KANAN: LEGENDA KETERANGAN SHIFT --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 rounded-lg h-100" style="border-top: 3px solid #6c757d;">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold text-dark m-0" style="font-size: 1.1rem;">Referensi Shift & Jam Kerja</h3>
                </div>
                <div class="card-body p-3 text-muted" style="font-size: 0.8rem; line-height: 1.8;">
                    <div class="d-flex flex-column custom-legend-list" style="gap: 10px;">
                        <div>
                            <span class="badge badge-light border font-mono px-2 py-1 mr-2" style="width: 45px; text-align: center;">R</span> 
                            <span>REGULER NON SHIFT DARI JAM 07:30 - 16:00 WIB (SENIN - KAMIS) / 07:30 - 16:30 WIB (JUMAT)</span>
                        </div>
                        <div>
                            <span class="badge badge-light border font-mono px-2 py-1 mr-2" style="width: 45px; text-align: center;">P</span> 
                            <span>DINAS TEKNISI DARI JAM 07:30 - 14:00 WIB</span>
                        </div>
                        <div>
                            <span class="badge badge-light border font-mono px-2 py-1 mr-2" style="width: 45px; text-align: center;">S</span> 
                            <span>DINAS TEKNISI DARI JAM 13:30 - 20:00 WIB</span>
                        </div>
                        <div>
                            <span class="badge badge-light border font-mono px-2 py-1 mr-2" style="width: 45px; text-align: center;">PS</span> 
                            <span>DINAS TEKNISI DARI JAM 07:30 - 20:00 WIB</span>
                        </div>
                        <div>
                            <span class="badge badge-light border font-mono px-2 py-1 mr-2" style="width: 45px; text-align: center;">PS1</span> 
                            <span>DINAS TEKNISI DARI JAM 07:30 - 20:00 WIB</span>
                        </div>
                        <div>
                            <span class="badge badge-light border font-mono px-2 py-1 mr-2" style="width: 45px; text-align: center;">TP</span> 
                            <span>DINAS TEKNISI DARI JAM 07:30 - 16:00 WIB</span>
                        </div>
                        <div>
                            <span class="badge badge-light border font-mono px-2 py-1 mr-2" style="width: 45px; text-align: center;">TP1</span> 
                            <span>DINAS TEKNISI DARI JAM 07:30 - 16:00 WIB</span>
                        </div>
                        <div>
                            <span class="badge badge-light border font-mono px-2 py-1 mr-2" style="width: 45px; text-align: center;">S2</span> 
                            <span>DINAS TEKNISI DARI JAM 10:30 - 18:30 WIB</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT UNTUK OTOMATISASI FORM --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shiftSelect = document.getElementById('shift');
        const jamInput = document.getElementById('jam');

        const pemetaanJam = {
            'R': '07:30 - 16:00 WIB (Senin-Kamis) / 07:30 - 16:30 WIB (Jumat)',
            'P': '07:30 - 14:00 WIB',
            'S': '13:30 - 20:00 WIB',
            'PS': '07:30 - 20:00 WIB',
            'PS1': '07:30 - 20:00 WIB',
            'TP': '07:30 - 16:00 WIB',
            'TP1': '07:30 - 16:00 WIB',
            'S2': '10:30 - 18:30 WIB'
        };

        shiftSelect.addEventListener('change', function() {
            const kodeShift = this.value;
            
            if (pemetaanJam[kodeShift]) {
                jamInput.value = pemetaanJam[kodeShift];
            } else {
                jamInput.value = '';
            }
        });
    });
</script>
@endsection