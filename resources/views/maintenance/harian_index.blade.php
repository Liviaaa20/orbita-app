@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            {{-- Header Halaman --}}
            <div class="mb-4">
                <h3 class="text-uppercase font-weight-bold" style="border-left: 5px solid #003366; padding-left: 15px; color: #003366;">
                    Maintenance Harian
                </h3>
            </div>

            {{-- Baris untuk membatasi lebar agar tidak kebesaran --}}
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom-0 text-center">
                            <h5 class="m-0 font-weight-bold text-dark"> Pengecekan Data Alat</h5>
                        </div>
                        
                        <div class="card-body px-4 pb-5 pt-2">
                            {{-- Action ini arahkan ke route yang menampilkan halaman Accordion Pengecekan --}}
                            <form action="{{ route('maintenance.show-pengecekan') }}" method="GET">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-muted small text-uppercase">Tanggal Pengecekan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-calendar-alt text-primary"></i></span>
                                        </div>
                                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" 
                                               class="form-control form-control-lg bg-light border-left-0 font-weight-bold" 
                                               style="border-radius: 0 8px 8px 0;">
                                    </div>
                                </div>

                                <div class="form-group mb-5">
                                    <label class="font-weight-bold text-muted small text-uppercase">Pilih Shift / Waktu</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-clock text-primary"></i></span>
                                        </div>
                                        <select name="waktu" class="form-control form-control-lg bg-light border-left-0 font-weight-bold" 
                                                style="border-radius: 0 8px 8px 0;">
                                            <option value="Pagi">Pagi</option>
                                            <option value="Siang">Siang</option>
                                            <option value="Malam">Malam</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Flag untuk identifikasi --}}
                                <input type="hidden" name="type" value="harian">

                                <hr class="mt-5">

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg mr-2 px-5">Batal</a>
                                    <button type="submit" class="btn btn-primary btn-lg px-5" style="background-color: #003366; border: none;">
                                        Proses
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="alert border-0 shadow-sm mt-4 p-3" style="border-radius: 10px; background-color: #eef7ff;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary mr-3 fa-lg"></i>
                            <p class="mb-0 small text-dark">
                                Anda akan diarahkan ke halaman <strong>Pengecekan Data Alat</strong> untuk memilih lokasi pengecekan setelah menekan tombol proses.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling input agar lebih clean */
    .form-control-lg {
        height: 55px;
        border: 1px solid #ebedef;
        font-size: 1rem;
    }
    .input-group-text {
        border: 1px solid #ebedef;
        color: #003366;
        padding-left: 20px;
        padding-right: 20px;
    }
    .form-control:focus {
        border-color: #003366;
        box-shadow: none;
        background-color: #fff !important;
    }
    .btn-primary:hover {
        background-color: #002244 !important;
        transform: translateY(-2px);
        transition: 0.3s;
    }
</style>
@endpush