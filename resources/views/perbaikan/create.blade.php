@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 text-center font-weight-bold">Input Permintaan Perbaikan</h5>
                    <p class="text-muted small text-center mb-0">Lengkapi detail informasi alat di bawah ini!</p>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('perbaikan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Tanggal Permintaan</label>
                                    <input type="text" class="form-control bg-light" value="{{ now()->format('d/m/Y') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">User Pelapor</label>
                                    <input type="text" class="form-control bg-light" value="{{ Auth::user()->name }}" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- TAMBAHAN: Dropdown Pilih Alat --}}
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Pilih Alat (Opsional)</label>
                            <select name="alat_id" class="form-control @error('alat_id') is-invalid @enderror select2">
                                <option value="">-- Gangguan Umum (Bukan Alat Spesifik) --</option>
                                @foreach($alats as $alat)
                                    <option value="{{ $alat->id }}" {{ old('alat_id') == $alat->id ? 'selected' : '' }}>
                                        {{ $alat->nama_alat }} ({{ $alat->lokasi }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted italic">Pilih alat jika kerusakan terjadi pada perangkat tertentu agar terekam di riwayat alat.</small>
                            @error('alat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Kategori Singkat</label>
                            <textarea name="kategori_perbaikan" class="form-control @error('kategori_perbaikan') is-invalid @enderror" rows="2" placeholder="Contoh: Kerusakan Sensor Angin / Gangguan Listrik..." required>{{ old('kategori_perbaikan') }}</textarea>
                            @error('kategori_perbaikan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Keterangan Detail Kerusakan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="4" placeholder="Jelaskan secara detail kronologi atau gejala kerusakan..." required>{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Foto Bukti Kerusakan</label>
                            <div class="custom-file">
                                <input type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror" id="customFile" accept="image/*">
                                <label class="custom-file-label" for="customFile">Pilih Gambar...</label>
                            </div>
                            <small class="text-muted">Format: JPG/PNG, maks 2MB</small>
                            @error('foto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('perbaikan.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Permintaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Agar nama file muncul saat dipilih
    $('#customFile').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Inisialisasi select2 jika Anda menggunakannya agar dropdown alat bisa di-search
    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }
    });
</script>
@endpush