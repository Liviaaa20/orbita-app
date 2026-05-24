@extends('layouts.master')

@section('content')
<div class="container-fluid">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2 text-success"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2 text-danger"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ATAS: Filter & Pencarian Data --}}
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-body p-3">
            <form action="{{ route('logbook.index') }}" method="GET" id="filterForm">
                <div class="row align-items-end font-weight-bold text-muted small text-uppercase">
                    <div class="col-md-2 mb-2 mb-md-0">
                        <label class="mb-1">Jenis Logbook</label>
                        <select name="jenis_logbook" class="form-control shadow-none border custom-select" onchange="this.form.submit()">
                            <option value="Semua Logbook">Semua Logbook</option>
                            @foreach($opsiJenisLogbook as $opt)
                                <option value="{{ $opt }}" {{ request('jenis_logbook') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <label class="mb-1">Jenis Alat</label>
                        <select name="jenis_alat" class="form-control shadow-none border custom-select" onchange="this.form.submit()">
                            <option value="Semua Logbook">Semua Logbook</option>
                            @foreach($opsiJenisAlat as $opt)
                                <option value="{{ $opt }}" {{ request('jenis_alat') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="mb-1">Lokasi/Tempat</label>
                        <select name="lokasi" class="form-control shadow-none border custom-select" onchange="this.form.submit()">
                            <option value="Semua Lokasi">Semua Lokasi</option>
                            @foreach($opsiLokasi as $opt)
                                <option value="{{ $opt }}" {{ request('lokasi') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="mb-1">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control shadow-none border" placeholder="Cari judul logbook..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-light border bg-white" type="submit"><i class="fas fa-search text-muted"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary btn-block font-weight-bold shadow-sm rounded-lg">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- UTAMA: Daftar Logbook & Tombol Aksi --}}
    <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold m-0 text-dark">Daftar Logbook</h5>
            <div>
                <button type="button" class="btn btn-light border font-weight-bold text-dark px-3 shadow-sm rounded-lg mr-2" data-toggle="modal" data-target="#modalTambahLogbook">
                    <i class="fas fa-plus mr-1 text-success"></i> Tambah Logbook
                </button>
                <button class="btn btn-light border font-weight-bold text-dark px-3 shadow-sm rounded-lg">
                    <i class="fas fa-download mr-1"></i> Unduh
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover text-center align-middle mb-0">
                <thead class="bg-light text-dark font-weight-bold text-uppercase small" style="font-size: 0.8rem;">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th class="text-left px-4" style="width: 25%;">Jenis Logbook</th>
                        <th style="width: 12%;">Jenis Alat</th>
                        <th class="text-left px-3" style="width: 25%;">Lokasi/Tempat</th>
                        <th style="width: 13%;">Periode Tersedia</th>
                        <th style="width: 8%;">Jumlah Data</th>
                        <th style="width: 12%;">Terakhir Diperbarui</th>
                        <th style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    @forelse($logbooks as $log)
                        <tr>
                            <td class="text-muted align-middle">{{ ($logbooks->currentPage() - 1) * $logbooks->perPage() + $loop->iteration }}</td>
                            <td class="text-left px-4 align-middle font-weight-bold text-dark text-uppercase">{{ $log->jenis_logbook }}</td>
                            <td class="align-middle"><span class="badge badge-light border px-2 py-1 text-muted">{{ $log->jenis_alat }}</span></td>
                            <td class="text-left px-3 align-middle text-muted">{{ $log->lokasi_tempat }}</td>
                            <td class="align-middle text-dark font-weight-medium">{{ $log->periode_tersedia }}</td>
                            <td class="align-middle text-dark">{{ number_format($log->jumlah_data, 0, ',', '.') }} entri</td>
                            <td class="align-middle text-muted">{{ $log->terakhir_diperbarui ? $log->terakhir_diperbarui->isoFormat('D MMMM YYYY') : '-' }}</td>
                            <td class="align-middle">
                                <div class="btn-group" role="group">
                                    {{-- Tombol Edit dengan pemicu fungsi data pass --}}
                                    <button type="button" 
                                            class="btn btn-sm btn-light border text-dark mx-1 p-2 rounded" 
                                            title="Edit"
                                            data-toggle="modal" 
                                            data-target="#modalEditLogbook"
                                            onclick="pemicuEdit(this)"
                                            data-id="{{ $log->id }}"
                                            data-jenis_logbook="{{ $log->jenis_logbook }}"
                                            data-jenis_alat="{{ $log->jenis_alat }}"
                                            data-lokasi_tempat="{{ $log->lokasi_tempat }}"
                                            data-periode_tersedia="{{ $log->periode_tersedia }}"
                                            data-jumlah_data="{{ $log->jumlah_data }}">
                                        <i class="fas fa-pencil-alt text-warning"></i>
                                    </button>

                                    {{-- Tombol Lihat Detail Catatan --}}
                                   <a href="{{ route('logbook.show', $log->id) }}" 
                                        class="btn btn-sm btn-light border text-dark mx-1 p-2 rounded" 
                                        title="Lihat Detail">
                                            <i class="fas fa-eye text-info"></i>
                                    </a>

                                    {{-- Tombol Unduh Laporan Ekspor --}}
                                    <a href="#" class="btn btn-sm btn-light border text-dark mx-1 p-2 rounded" title="Unduh">
                                        <i class="fas fa-download text-success"></i>
                                    </a>

                                    {{-- Tombol Hapus Data dengan Proteksi --}}
                                    <form action="#" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data logbook ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="btn btn-sm btn-light border text-dark mx-1 p-2 rounded btn-delete"
                                            title="Hapus"
                                            data-toggle="modal"
                                            data-target="#modalDeleteLogbook"
                                            data-id="{{ $log->id }}"
                                            data-nama="{{ $log->jenis_logbook }}">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open mb-3 d-block" style="font-size: 2.5rem;"></i>
                                Belum ada data logbook yang terdaftar atau cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Tabel: Pagination Terintegrasi Dinamis --}}
        @if($logbooks->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                {{ $logbooks->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

{{-- ==================== POP-UP MODAL TAMBAH LOGBOOK ==================== --}}
<div class="modal fade" id="modalTambahLogbook" tabindex="-1" role="dialog" aria-labelledby="modalTambahLogbookLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title font-weight-bold text-dark" id="modalTambahLogbookLabel">Tambah Logbook Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('logbook.store') }}" method="POST">
                @csrf
                <div class="modal-body text-dark font-weight-bold small text-muted text-uppercase">
                    <div class="form-group">
                        <label class="mb-1">Nama / Jenis Logbook</label>
                        <input type="text" name="jenis_logbook" class="form-control text-dark font-weight-normal shadow-none" placeholder="Contoh: LOG BOOK PERALATAN KONVENSIONAL" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Jenis Alat</label>
                        <input type="text" name="jenis_alat" class="form-control text-dark font-weight-normal shadow-none" placeholder="Contoh: Konvensional / Otomatis" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Lokasi / Tempat Penempatan</label>
                        <input type="text" name="lokasi_tempat" class="form-control text-dark font-weight-normal shadow-none" placeholder="Contoh: Stasiun Maritim Tanjung Emas" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Rentang Periode Tersedia</label>
                        <input type="text" name="periode_tersedia" class="form-control text-dark font-weight-normal shadow-none" placeholder="Contoh: Jan 2026 - April 2026" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Jumlah Data Awal (Entri)</label>
                        <input type="number" name="jumlah_data" class="form-control text-dark font-weight-normal shadow-none" value="0" min="0" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary font-weight-bold shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== POP-UP MODAL EDIT LOGBOOK ==================== --}}
<div class="modal fade" id="modalEditLogbook" tabindex="-1" role="dialog" aria-labelledby="modalEditLogbookLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title font-weight-bold text-dark" id="modalEditLogbookLabel">Perbarui Data Logbook</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="formEditLogbook">
                @csrf
                @method('PUT')
                <div class="modal-body text-dark font-weight-bold small text-muted text-uppercase">
                    <div class="form-group">
                        <label class="mb-1">Nama / Jenis Logbook</label>
                        <input type="text" name="jenis_logbook" id="edit_jenis_logbook" class="form-control text-dark font-weight-normal shadow-none" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Jenis Alat</label>
                        <input type="text" name="jenis_alat" id="edit_jenis_alat" class="form-control text-dark font-weight-normal shadow-none" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Lokasi / Tempat Penempatan</label>
                        <input type="text" name="lokasi_tempat" id="edit_lokasi_tempat" class="form-control text-dark font-weight-normal shadow-none" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Rentang Periode Tersedia</label>
                        <input type="text" name="periode_tersedia" id="edit_periode_tersedia" class="form-control text-dark font-weight-normal shadow-none" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Jumlah Data (Entri)</label>
                        <input type="number" name="jumlah_data" id="edit_jumlah_data" class="form-control text-dark font-weight-normal shadow-none" min="0" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary font-weight-bold shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- ==================== POP-UP MODAL DELETE LOGBOOK ==================== --}}
<div class="modal fade" id="modalDeleteLogbook" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLogbookLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">

            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title font-weight-bold" id="modalDeleteLogbookLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Konfirmasi Hapus
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body text-center py-4">

                <div class="mb-3">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 4rem;"></i>
                </div>

                <h5 class="font-weight-bold text-dark">
                    Hapus Data Logbook?
                </h5>

                <p class="text-muted mb-1">
                    Data berikut akan dihapus permanen:
                </p>

                <div class="alert alert-light border mt-3">
                    <strong id="delete_nama_logbook"></strong>
                </div>

                <small class="text-danger">
                    Tindakan ini tidak dapat dibatalkan.
                </small>
            </div>

            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                    Batal
                </button>

                <form method="POST" id="formDeleteLogbook">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger px-4 font-weight-bold">
                        <i class="fas fa-trash-alt mr-1"></i>
                        Ya, Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function pemicuEdit(element) {

        // Debug
        console.log('Edit clicked');

        const id = element.getAttribute('data-id');
        const jenisLogbook = element.getAttribute('data-jenis_logbook');
        const jenisAlat = element.getAttribute('data-jenis_alat');
        const lokasiTempat = element.getAttribute('data-lokasi_tempat');
        const periodeTersedia = element.getAttribute('data-periode_tersedia');
        const jumlahData = element.getAttribute('data-jumlah_data');

        document.getElementById('edit_jenis_logbook').value = jenisLogbook;
        document.getElementById('edit_jenis_alat').value = jenisAlat;
        document.getElementById('edit_lokasi_tempat').value = lokasiTempat;
        document.getElementById('edit_periode_tersedia').value = periodeTersedia;
        document.getElementById('edit_jumlah_data').value = jumlahData;

        document.getElementById('formEditLogbook')
            .setAttribute('action', `/logbook/update/${id}`);
    }
        // DELETE MODAL
    $(document).on('click', '.btn-delete', function () {

        let id = $(this).data('id');
        let nama = $(this).data('nama');

        $('#delete_nama_logbook').text(nama);

        $('#formDeleteLogbook').attr('action', '/logbook/delete/' + id);
    });
</script>
@endpush