@extends('layouts.master')

@section('content')
@php
    $userRole      = strtolower(Auth::user()->role->nama_role ?? '');
    $isTeknisi       = $userRole === 'teknisi';
    $isKapok = in_array($userRole, ['kepalakelompok', 'kepala kelompok', 'kepala_kelompok', 'kapok']);
    $isKoordinator = $userRole === 'koordinator';
@endphp

<style>
    .btn-gradient-submit {
        background: linear-gradient(135deg, #003366, #004d99);
        border: none; color: #fff;
        padding: 6px 14px; border-radius: 8px;
        font-weight: 600; font-size: 13px;
    }
    .btn-gradient-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,51,102,0.3);
        color: #fff;
    }
    .stat-card { border-radius: 12px; transition: all .25s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .icon-stat {
        width: 45px; height: 45px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
</style>

<div class="container-fluid px-4 py-3">

    {{-- ===== HEADER ===== --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="m-0 text-dark font-weight-bold" style="font-size: 1.5rem;">
                <i class="fas fa-book-open mr-2" style="color: #003366;"></i>Logbook Operasional
            </h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Manajemen data logbook laboratorium</p>
        </div>
        <div class="col-md-6">
            <ol class="breadcrumb float-md-right bg-transparent m-0 p-0" style="font-size: 0.8rem;">
                <li class="breadcrumb-item text-muted">Master Data</li>
                <li class="breadcrumb-item active font-weight-bold" style="color: #003366;">Logbook</li>
            </ol>
        </div>
    </div>

    {{-- ===== NOTIFIKASI ===== --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center" style="border-radius: 8px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" style="border-radius: 8px;">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- ===== STATISTIK ===== --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="border-left: 4px solid #003366;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-stat" style="background: #003366; color: #fff;"><i class="fas fa-list"></i></div>
                        <div class="ml-3">
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Total Logbook</p>
                            <h5 class="mb-0 font-weight-bold">{{ $logbooks->total() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="border-left: 4px solid #ffc107;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-stat" style="background: #ffc107; color: #fff;"><i class="fas fa-clock"></i></div>
                        <div class="ml-3">
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Menunggu</p>
                            <h5 class="mb-0 font-weight-bold">
                                {{ $logbooks->where('status', 'pending_kapok')->count() + $logbooks->where('status', 'pending_koordinator')->count() }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="border-left: 4px solid #28a745;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-stat" style="background: #28a745; color: #fff;"><i class="fas fa-check-circle"></i></div>
                        <div class="ml-3">
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Disetujui</p>
                            <h5 class="mb-0 font-weight-bold">{{ $logbooks->where('status', 'approved_final')->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="border-left: 4px solid #dc3545;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-stat" style="background: #dc3545; color: #fff;"><i class="fas fa-times-circle"></i></div>
                        <div class="ml-3">
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Ditolak</p>
                            <h5 class="mb-0 font-weight-bold">
                                {{ $logbooks->whereIn('status', ['rejected_kapok', 'rejected_koordinator'])->count() }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FILTER ===== --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('logbook.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label class="text-muted font-weight-semibold mb-2" style="font-size: 0.8rem;">Jenis Logbook</label>
                        <select name="jenis_logbook" class="form-control" style="border-radius: 8px; height: 42px;" onchange="this.form.submit()">
                            <option value="Semua Logbook">Semua</option>
                            @foreach($opsiJenisLogbook as $opt)
                                <option value="{{ $opt }}" {{ request('jenis_logbook') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        {{-- DIUBAH: filter kategori, bukan sub_kategori --}}
                        <label class="text-muted font-weight-semibold mb-2" style="font-size: 0.8rem;">Kategori</label>
                        <select name="kategori_id" class="form-control" style="border-radius: 8px; height: 42px;" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="text-muted font-weight-semibold mb-2" style="font-size: 0.8rem;">Status</label>
                        <select name="status" class="form-control" style="border-radius: 8px; height: 42px;" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="draft"                {{ request('status') == 'draft'                ? 'selected' : '' }}>Draft</option>
                            <option value="pending_kapok"        {{ request('status') == 'pending_kapok'        ? 'selected' : '' }}>Menunggu Kepala Kelompok</option>
                            <option value="pending_koordinator"  {{ request('status') == 'pending_koordinator'  ? 'selected' : '' }}>Menunggu Koordinator</option>
                            <option value="approved_final"       {{ request('status') == 'approved_final'       ? 'selected' : '' }}>Disetujui Final</option>
                            <option value="rejected_kapok"       {{ request('status') == 'rejected_kapok'       ? 'selected' : '' }}>Ditolak Kepala Kelompok</option>
                            <option value="rejected_koordinator" {{ request('status') == 'rejected_koordinator' ? 'selected' : '' }}>Ditolak Koordinator</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted font-weight-semibold mb-2" style="font-size: 0.8rem;">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   style="border-radius: 8px 0 0 8px; height: 42px;"
                                   placeholder="Cari logbook..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-light border" type="submit" style="border-radius: 0 8px 8px 0;">
                                    <i class="fas fa-search text-muted"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        @if($isTeknisi)
                            <button type="button" class="btn btn-gradient-submit btn-block shadow-sm"
                                    data-toggle="modal" data-target="#modalTambahLogbook">
                                <i class="fas fa-plus mr-2"></i>Tambah
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== TABEL ===== --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header border-0 bg-white py-3">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-list text-primary mr-2"></i>Daftar Logbook
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 0.875rem;">
                    <thead class="bg-light font-weight-semibold border-bottom">
                        <tr>
                            <th class="border-0 py-3 pl-4" style="width: 50px;">No</th>
                            <th class="border-0 py-3">Jenis Logbook</th>
                            <th class="border-0 py-3">Kategori</th>  {{-- DIUBAH dari Sub Kategori --}}
                            <th class="border-0 py-3">Lokasi</th>
                            <th class="border-0 py-3">Periode</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Approval</th>
                            <th class="border-0 py-3 text-center pr-4" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logbooks as $log)
                        <tr>
                            <td class="py-3 pl-4 text-muted font-weight-bold">
                                {{ ($logbooks->currentPage() - 1) * $logbooks->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-3">
                                <div class="font-weight-semibold">{{ $log->jenis_logbook }}</div>
                                <small class="text-muted">{{ $log->jenis_alat }}</small>
                            </td>
                            <td class="py-3">
                                {{-- DIUBAH: tampilkan kategori --}}
                                @if($log->kategori)
                                    <span class="badge px-2 py-1"
                                          style="background: #e3f2fd; color: #003366; border-radius: 6px;">
                                        <i class="fas fa-layer-group mr-1"></i>{{ $log->kategori->nama_kategori }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3 text-muted">{{ $log->lokasi_tempat }}</td>
                            <td class="py-3">{{ $log->periode_tersedia }}</td>
                            <td class="py-3">
                                @switch($log->status)
                                    @case('draft')
                                        <span class="badge badge-secondary">Draft</span> @break
                                    @case('pending_kapok')
                                        <span class="badge badge-warning">Menunggu Kepala Kelompok</span> @break
                                    @case('pending_koordinator')
                                        <span class="badge badge-warning">Menunggu Koord</span> @break
                                    @case('approved_final')
                                        <span class="badge badge-success">Disetujui</span> @break
                                    @case('rejected_kapok')
                                        <span class="badge badge-danger">Ditolak Kepala Kelompok</span> @break
                                    @case('rejected_koordinator')
                                        <span class="badge badge-danger">Ditolak Koord</span> @break
                                    @default
                                        <span class="badge badge-light">{{ strtoupper($log->status) }}</span>
                                @endswitch
                            </td>
                            <td class="py-3" style="font-size: 0.8rem;">
                                @if($log->approved_kapok_by && $log->approvedKapokOleh)
                                    <div class="text-success">
                                        <i class="fas fa-check mr-1"></i>{{ $log->approvedKapokOleh->name }}
                                    </div>
                                @endif
                                @if($log->approved_koordinator_by && $log->approvedKoordinatorOleh)
                                    <div class="text-success">
                                        <i class="fas fa-check-double mr-1"></i>{{ $log->approvedKoordinatorOleh->name }}
                                    </div>
                                @endif
                                @if(empty($log->approved_kapok_by) && empty($log->approved_koordinator_by))
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3 text-center pr-4">
                                <div class="d-flex justify-content-center" style="gap: 4px;">
                                    <a href="{{ route('logbook.show', $log->id) }}"
                                       class="btn btn-sm"
                                       style="border-radius: 6px; background: #17a2b8; color: #fff;"
                                       title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($isTeknisi && $log->status == 'draft')
                                        <button type="button" class="btn btn-sm btn-light border" title="Edit"
                                                data-toggle="modal" data-target="#modalEditLogbook"
                                                onclick="pemicuEdit(this)"
                                                data-id="{{ $log->id }}"
                                                data-kategori_id="{{ $log->kategori_id }}"
                                                data-jenis_logbook="{{ $log->jenis_logbook }}"
                                                data-jenis_alat="{{ $log->jenis_alat }}"
                                                data-lokasi_tempat="{{ $log->lokasi_tempat }}"
                                                data-periode_tersedia="{{ $log->periode_tersedia }}">
                                            <i class="fas fa-pencil-alt text-warning"></i>
                                        </button>

                                        <button type="button"
                                                class="btn btn-sm btn-gradient-submit"
                                                title="Ajukan"
                                                data-toggle="modal"
                                                data-target="#modalSubmitLogbook"
                                                data-id="{{ $log->id }}"
                                                data-jenis="{{ $log->jenis_logbook }}">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-light border btn-delete"
                                                title="Hapus" data-toggle="modal" data-target="#modalDeleteLogbook"
                                                data-id="{{ $log->id }}"
                                                data-nama="{{ $log->jenis_logbook }}">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button>
                                    @endif

                                    @if($isKapok && $log->status == 'pending_kapok')
                                        <button type="button" class="btn btn-sm btn-success" title="Setuju"
                                                data-toggle="modal" data-target="#modalApproveKapok"
                                                onclick="setModalId('modalApproveKapokForm', '{{ route('logbook.approve-kapok', $log->id) }}')">>
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" title="Tolak"
                                                data-toggle="modal" data-target="#modalRejectKapok"
                                                onclick="setModalId('modalRejectKapokForm', '{{ route('logbook.reject-kapok', $log->id) }}')">>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if($isKoordinator && $log->status == 'pending_koordinator')
                                        <button type="button" class="btn btn-sm btn-success" title="Setuju Final"
                                                data-toggle="modal" data-target="#modalApproveKoordinator"
                                                onclick="setModalId('modalApproveKoordinatorForm', '{{ route('logbook.approve-koordinator', $log->id) }}')">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" title="Tolak"
                                                data-toggle="modal" data-target="#modalRejectKoordinator"
                                                onclick="setModalId('modalRejectKoordinatorForm', '{{ route('logbook.reject-koordinator', $log->id) }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if($log->status == 'approved_final')
                                        <a href="{{ route('download-pdf', $log->id) }}"
                                           class="btn btn-sm"
                                           style="border-radius: 6px; background: #dc3545; color: #fff;"
                                           title="Download PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-book-open fa-2x mb-3 d-block text-light"></i>
                                Belum ada data logbook
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logbooks->hasPages())
        <div class="card-footer bg-light py-3">
            {{ $logbooks->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

{{-- ===== MODAL TAMBAH ===== --}}
<div class="modal fade" id="modalTambahLogbook" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0" style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold">Tambah Logbook Baru</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('logbook.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- DIUBAH: pilih Kategori --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_id" class="form-control" style="border-radius: 8px; height: 46px;" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Jenis Logbook <span class="text-danger">*</span></label>
                        <input type="text" name="jenis_logbook" class="form-control"
                               style="border-radius: 8px; height: 46px;"
                               placeholder="Contoh: LOG BOOK PERALATAN KONVENSIONAL" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Jenis Alat <span class="text-danger">*</span></label>
                        <input type="text" name="jenis_alat" class="form-control"
                               style="border-radius: 8px; height: 46px;"
                               placeholder="Contoh: Konvensional / Otomatis" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi_tempat" class="form-control"
                               style="border-radius: 8px; height: 46px;"
                               placeholder="Contoh: Stasiun Maritim Tanjung Emas" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Periode <span class="text-danger">*</span></label>
                        <input type="text" name="periode_tersedia" class="form-control"
                               style="border-radius: 8px; height: 46px;"
                               placeholder="Contoh: Jan 2026 - Des 2026" required>
                    </div>
                </div>
                <div class="modal-footer border-0" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px;" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white" style="border-radius: 8px; background: #003366;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL EDIT ===== --}}
<div class="modal fade" id="modalEditLogbook" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0" style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold">Perbarui Logbook</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="" method="POST" id="formEditLogbook">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- DIUBAH: pilih Kategori --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_id" id="edit_kategori_id" class="form-control"
                                style="border-radius: 8px; height: 46px;" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Jenis Logbook <span class="text-danger">*</span></label>
                        <input type="text" name="jenis_logbook" id="edit_jenis_logbook" class="form-control"
                               style="border-radius: 8px; height: 46px;" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Jenis Alat <span class="text-danger">*</span></label>
                        <input type="text" name="jenis_alat" id="edit_jenis_alat" class="form-control"
                               style="border-radius: 8px; height: 46px;" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi_tempat" id="edit_lokasi_tempat" class="form-control"
                               style="border-radius: 8px; height: 46px;" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Periode <span class="text-danger">*</span></label>
                        <input type="text" name="periode_tersedia" id="edit_periode_tersedia" class="form-control"
                               style="border-radius: 8px; height: 46px;" required>
                    </div>
                </div>
                <div class="modal-footer border-0" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px;" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white" style="border-radius: 8px; background: #003366;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL DELETE ===== --}}
<div class="modal fade" id="modalDeleteLogbook" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-danger text-white border-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Hapus Logbook</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt text-danger mb-3 d-block" style="font-size: 3rem;"></i>
                <h5 class="font-weight-bold">Hapus Data Logbook?</h5>
                <p class="text-muted">Data berikut akan dihapus permanen:</p>
                <div class="alert alert-light border mt-3">
                    <strong id="delete_nama_logbook"></strong>
                </div>
                <small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer border-0" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 8px;" data-dismiss="modal">Batal</button>
                <form method="POST" id="formDeleteLogbook">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-trash-alt mr-1"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ajukan ke Kepala Kelompok -->
<div class="modal fade" id="modalSubmitLogbook" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Ajukan Logbook
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="formSubmitLogbook" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-file-upload fa-3x text-primary"></i>
                    </div>

                    <p class="text-center mb-1">
                        Apakah Anda yakin ingin mengajukan logbook:
                    </p>

                    <h6 id="namaLogbookSubmit"
                        class="text-center font-weight-bold text-primary">
                    </h6>

                    <p class="text-center text-muted mt-3 mb-0">
                        Logbook akan dikirim ke <strong>Kepala Kelompok (kapok)</strong>
                        untuk proses approval.
                    </p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button"
                            class="btn btn-light border"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Ya, Ajukan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@include('logbook._modals_approval')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function pemicuEdit(element) {
        document.getElementById('edit_kategori_id').value    = element.getAttribute('data-kategori_id');
        document.getElementById('edit_jenis_logbook').value  = element.getAttribute('data-jenis_logbook');
        document.getElementById('edit_jenis_alat').value     = element.getAttribute('data-jenis_alat');
        document.getElementById('edit_lokasi_tempat').value  = element.getAttribute('data-lokasi_tempat');
        document.getElementById('edit_periode_tersedia').value = element.getAttribute('data-periode_tersedia');
        document.getElementById('formEditLogbook').setAttribute('action', '/logbook/update/' + element.getAttribute('data-id'));
    }

    function setModalId(formId, action) {
        document.getElementById(formId).setAttribute('action', action);
    }

    $(document).on('click', '.btn-delete', function () {
        $('#delete_nama_logbook').text($(this).data('nama'));
        $('#formDeleteLogbook').attr('action', '/logbook/delete/' + $(this).data('id'));
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil', text: '{{ session('success') }}', timer: 3000, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal', text: '{{ session('error') }}', timer: 3000, showConfirmButton: false });
    @endif
    $('#modalSubmitLogbook').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);

        let id = button.data('id');
        let jenis = button.data('jenis');

        $('#namaLogbookSubmit').text(jenis);

        $('#formSubmitLogbook').attr(
            'action',
            '/logbook/submit/' + id
        );
    });
</script>
@endpush