@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="mb-4">
                <h3 class="text-uppercase font-weight-bold" style="border-left: 5px solid #003366; padding-left: 15px;">
                    Maintenance Mingguan
                </h3>
            </div>

            <div class="card shadow-sm">
                <div class="card-header text-center bg-light">
                    <h5 class="m-0 font-weight-bold text-muted">Pengecekan Data Alat</h5>
                </div>
                
                <div class="card-body py-5 px-4">
                <form action="{{ route('maintenance.show-pengecekan') }}" method="GET">
                <input type="hidden" name="type" value="mingguan">
                @php 
        $hariIni = \Carbon\Carbon::now()->translatedFormat('l'); 
    @endphp
    <input type="hidden" name="waktu" value="{{ $hariIni }}">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="font-weight-bold text-secondary">Pilih Tanggal Pengecekan Mingguan</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control form-control-lg bg-light">
            </div>
        </div>
    </div>

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
        </div>
    </div>
</div>
@endsection