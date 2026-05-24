@extends('layouts.master')

@section('content')
@php
    $userRole      = strtolower(Auth::user()->role->nama_role ?? '');
    $isAdmin       = $userRole === 'admin';
    $isKanit       = in_array($userRole, ['kepala unit', 'kepala_unit', 'kanit']);
    $isKoordinator = $userRole === 'koordinator';
@endphp
<style>
    .btn-gradient-submit{
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        border: none;
        color: #fff;
        padding: 6px 14px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        transition: all .25s ease;
    }

    .btn-gradient-submit:hover{
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(13,110,253,.25);
        color: #fff;
    }

    .btn-gradient-submit:active{
        transform: scale(.98);
    }

    .btn-gradient-submit i{
        font-size: 12px;
    }
</style>
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-body p-3">
            <form action="{{ route('logbook.index') }}" method="GET">
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
                    <div class="col-md-2 mb-2 mb-md-0">
                        <label class="mb-1">Status</label>
                        <select name="status" class="form-control shadow-none border custom-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="draft"                {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending_kanit"        {{ request('status') == 'pending_kanit' ? 'selected' : '' }}>Menunggu Kanit</option>
                            <option value="approved_kanit"       {{ request('status') == 'approved_kanit' ? 'selected' : '' }}>Disetujui Kanit</option>
                            <option value="rejected_kanit"       {{ request('status') == 'rejected_kanit' ? 'selected' : '' }}>Ditolak Kanit</option>
                            <option value="pending_koordinator"  {{ request('status') == 'pending_koordinator' ? 'selected' : '' }}>Menunggu Koordinator</option>
                            <option value="approved_final"       {{ request('status') == 'approved_final' ? 'selected' : '' }}>Disetujui Final</option>
                            <option value="rejected_koordinator" {{ request('status') == 'rejected_koordinator' ? 'selected' : '' }}>Ditolak Koordinator</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="mb-1">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control shadow-none border"
                                   placeholder="Cari judul logbook..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-light border bg-white" type="submit">
                                    <i class="fas fa-search text-muted"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary btn-block font-weight-bold shadow-sm rounded-lg">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold m-0 text-dark">Daftar Logbook</h5>
            @if($isAdmin)
                <button type="button" class="btn btn-light border font-weight-bold text-dark px-3 shadow-sm rounded-lg"
                        data-toggle="modal" data-target="#modalTambahLogbook">
                    <i class="fas fa-plus mr-1 text-success"></i> Tambah Logbook
                </button>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover text-center align-middle mb-0">
                <thead class="bg-light text-dark font-weight-bold text-uppercase small" style="font-size:0.8rem;">
                    <tr>
                        <th style="width:4%;">No</th>
                        <th class="text-left px-3" style="width:20%;">Jenis Logbook</th>
                        <th style="width:12%;">Sub Kategori</th>
                        <th style="width:10%;">Lokasi</th>
                        <th style="width:10%;">Periode</th>
                        <th style="width:12%;">Status</th>
                        <th style="width:12%;">Approval</th>
                        <th style="width:10%;">Diperbarui</th>
                        <th style="width:10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size:0.82rem;">
                    @forelse($logbooks as $log)
                        <tr>
                            <td class="text-muted align-middle">
                                {{ ($logbooks->currentPage() - 1) * $logbooks->perPage() + $loop->iteration }}
                            </td>

                            {{-- Jenis Logbook --}}
                            <td class="text-left px-3 align-middle font-weight-bold text-dark text-uppercase">
                                {{ $log->jenis_logbook }}
                                <div class="text-muted font-weight-normal" style="font-size:0.75rem;">
                                    {{ $log->jenis_alat }}
                                </div>
                            </td>

                            {{-- Sub Kategori --}}
                            <td class="align-middle">
                                @if($log->subKategori)
                                    <span class="badge badge-info px-2 py-1">{{ $log->subKategori->nama_sub_kategori }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- Lokasi --}}
                            <td class="align-middle text-muted" style="font-size:0.78rem;">
                                {{ $log->lokasi_tempat }}
                            </td>

                            {{-- Periode --}}
                            <td class="align-middle text-dark">{{ $log->periode_tersedia }}</td>

                            {{-- Status Badge --}}
                            <td class="align-middle">
                                <span class="badge badge-{{ $log->getBadgeStatus() }} px-2 py-1">
                                    {{ $log->getLabelStatus() }}
                                </span>
                            </td>

                            {{-- Info Approval --}}
                            <td class="align-middle" style="font-size:0.75rem;">
                                @if($log->approvedKanitOleh)
                                    <div class="text-muted">
                                        <i class="fas fa-user-check text-info mr-1"></i>
                                        Kanit: <strong>{{ $log->approvedKanitOleh->name }}</strong>
                                    </div>
                                @endif
                                @if($log->approvedKoordinatorOleh)
                                    <div class="text-muted mt-1">
                                        <i class="fas fa-user-check text-success mr-1"></i>
                                        Koord: <strong>{{ $log->approvedKoordinatorOleh->name }}</strong>
                                    </div>
                                @endif
                                @if(!$log->approvedKanitOleh && !$log->approvedKoordinatorOleh)
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Diperbarui --}}
                            <td class="align-middle text-muted">
                                {{ $log->terakhir_diperbarui ? $log->terakhir_diperbarui->isoFormat('D MMM YYYY') : '-' }}
                            </td>

                            {{-- Aksi --}}
                            <td class="align-middle">
                                <div class="d-flex justify-content-center flex-wrap" style="gap:2px;">

                                    {{-- Lihat Detail (semua role) --}}
                                    <a href="{{ route('logbook.show', $log->id) }}"
                                       class="btn btn-sm btn-light border p-2 rounded" title="Lihat Detail">
                                        <i class="fas fa-eye text-info"></i>
                                    </a>

                                    {{-- ADMIN: Edit & Submit & Hapus --}}
                                    @if($isAdmin)
                                        @if($log->bisaSubmit())
                                            <button type="button"
                                                    class="btn btn-sm btn-light border p-2 rounded"
                                                    title="Edit"
                                                    data-toggle="modal"
                                                    data-target="#modalEditLogbook"
                                                    onclick="pemicuEdit(this)"
                                                    data-id="{{ $log->id }}"
                                                    data-sub_kategori_id="{{ $log->sub_kategori_id }}"
                                                    data-jenis_logbook="{{ $log->jenis_logbook }}"
                                                    data-jenis_alat="{{ $log->jenis_alat }}"
                                                    data-lokasi_tempat="{{ $log->lokasi_tempat }}"
                                                    data-periode_tersedia="{{ $log->periode_tersedia }}">
                                                <i class="fas fa-pencil-alt text-warning"></i>
                                            </button>

                                        <form action="{{ route('logbook.submit', $log->id) }}" method="POST" class="d-inline">
                                            @csrf

                                            <button type="submit"
                                                    class="btn btn-sm btn-gradient-submit shadow-sm"
                                                    title="Ajukan ke Kepala Unit"
                                                    onclick="return confirmSubmitLogbook(event)">
                                                <i class="fas fa-paper-plane mr-1"></i>
                                                Ajukan
                                            </button>
                                        </form>
                                        @endif

                                        @if($log->bisaSubmit())
                                            <button type="button"
                                                    class="btn btn-sm btn-light border p-2 rounded btn-delete"
                                                    title="Hapus"
                                                    data-toggle="modal"
                                                    data-target="#modalDeleteLogbook"
                                                    data-id="{{ $log->id }}"
                                                    data-nama="{{ $log->jenis_logbook }}">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </button>
                                        @endif
                                    @endif

                                    {{-- KANIT: Approve / Reject --}}
                                    @if($isKanit && $log->status === 'pending_kanit')
                                        <button type="button"
                                                class="btn btn-sm btn-success border p-2 rounded"
                                                title="Setujui"
                                                data-toggle="modal"
                                                data-target="#modalApproveKanit"
                                                onclick="setModalId('modalApproveKanitForm', '{{ route('logbook.approve-kanit', $log->id) }}')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-danger border p-2 rounded"
                                                title="Tolak"
                                                data-toggle="modal"
                                                data-target="#modalRejectKanit"
                                                onclick="setModalId('modalRejectKanitForm', '{{ route('logbook.reject-kanit', $log->id) }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    {{-- KOORDINATOR: Approve / Reject --}}
                                    @if($isKoordinator && $log->status === 'pending_koordinator')
                                        <button type="button"
                                                class="btn btn-sm btn-success border p-2 rounded"
                                                title="Setujui Final"
                                                data-toggle="modal"
                                                data-target="#modalApproveKoordinator"
                                                onclick="setModalId('modalApproveKoordinatorForm', '{{ route('logbook.approve-koordinator', $log->id) }}')">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-danger border p-2 rounded"
                                                title="Tolak"
                                                data-toggle="modal"
                                                data-target="#modalRejectKoordinator"
                                                onclick="setModalId('modalRejectKoordinatorForm', '{{ route('logbook.reject-koordinator', $log->id) }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    {{-- Download PDF (hanya approved_final) --}}
                                    @if($log->bisaDownload())
                                        <a href="{{ route('logbook.show', $log->id) }}?print=1"
                                           class="btn btn-sm btn-light border p-2 rounded" title="Download PDF">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                        </a>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open mb-3 d-block" style="font-size:2.5rem;"></i>
                                Belum ada data logbook yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logbooks->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                {{ $logbooks->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

{{-- ===== MODAL TAMBAH ===== --}}
<div class="modal fade" id="modalTambahLogbook" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title font-weight-bold text-dark">Tambah Logbook Baru</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('logbook.store') }}" method="POST">
                @csrf
                <div class="modal-body small text-uppercase font-weight-bold text-muted">
                    <div class="form-group">
                        <label class="mb-1">Sub Kategori Alat <span class="text-danger">*</span></label>
                        <select name="sub_kategori_id" class="form-control shadow-none text-dark font-weight-normal" required>
                            <option value="">-- Pilih Sub Kategori --</option>
                            @foreach($subKategoris as $sub)
                                <option value="{{ $sub->id }}">
                                    {{ $sub->nama_sub_kategori }}
                                    @if($sub->kategori)({{ $sub->kategori->nama_kategori }})@endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted font-weight-normal text-lowercase">
                            Alat di sub kategori ini otomatis masuk ke logbook.
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Nama / Jenis Logbook <span class="text-danger">*</span></label>
                        <input type="text" name="jenis_logbook" class="form-control text-dark font-weight-normal shadow-none"
                               placeholder="Contoh: LOG BOOK PERALATAN KONVENSIONAL" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Jenis Alat <span class="text-danger">*</span></label>
                        <input type="text" name="jenis_alat" class="form-control text-dark font-weight-normal shadow-none"
                               placeholder="Contoh: Konvensional / Otomatis" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Lokasi / Tempat <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi_tempat" class="form-control text-dark font-weight-normal shadow-none"
                               placeholder="Contoh: Stasiun Maritim Tanjung Emas" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Rentang Periode <span class="text-danger">*</span></label>
                        <input type="text" name="periode_tersedia" class="form-control text-dark font-weight-normal shadow-none"
                               placeholder="Contoh: Jan 2026 - Des 2026" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL EDIT ===== --}}
<div class="modal fade" id="modalEditLogbook" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title font-weight-bold text-dark">Perbarui Data Logbook</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="" method="POST" id="formEditLogbook">
                @csrf
                @method('PUT')
                <div class="modal-body small text-uppercase font-weight-bold text-muted">
                    <div class="form-group">
                        <label class="mb-1">Sub Kategori Alat <span class="text-danger">*</span></label>
                        <select name="sub_kategori_id" id="edit_sub_kategori_id"
                                class="form-control shadow-none text-dark font-weight-normal" required>
                            <option value="">-- Pilih Sub Kategori --</option>
                            @foreach($subKategoris as $sub)
                                <option value="{{ $sub->id }}">
                                    {{ $sub->nama_sub_kategori }}
                                    @if($sub->kategori)({{ $sub->kategori->nama_kategori }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Nama / Jenis Logbook <span class="text-danger">*</span></label>
                        <input type="text" name="jenis_logbook" id="edit_jenis_logbook"
                               class="form-control text-dark font-weight-normal shadow-none" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Jenis Alat <span class="text-danger">*</span></label>
                        <input type="text" name="jenis_alat" id="edit_jenis_alat"
                               class="form-control text-dark font-weight-normal shadow-none" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Lokasi / Tempat <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi_tempat" id="edit_lokasi_tempat"
                               class="form-control text-dark font-weight-normal shadow-none" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-1">Rentang Periode <span class="text-danger">*</span></label>
                        <input type="text" name="periode_tersedia" id="edit_periode_tersedia"
                               class="form-control text-dark font-weight-normal shadow-none" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL DELETE ===== --}}
<div class="modal fade" id="modalDeleteLogbook" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt text-danger mb-3 d-block" style="font-size:4rem;"></i>
                <h5 class="font-weight-bold text-dark">Hapus Data Logbook?</h5>
                <p class="text-muted mb-1">Data berikut akan dihapus permanen:</p>
                <div class="alert alert-light border mt-3">
                    <strong id="delete_nama_logbook"></strong>
                </div>
                <small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                <form method="POST" id="formDeleteLogbook">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold">
                        <i class="fas fa-trash-alt mr-1"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL APPROVE KANIT ===== --}}
<div class="modal fade" id="modalApproveKanit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-check-circle mr-2"></i> Setujui Logbook
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalApproveKanitForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Logbook akan diteruskan ke Koordinator setelah disetujui.</p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">Catatan (opsional)</label>
                        <textarea name="catatan_kanit" class="form-control shadow-none" rows="3"
                                  placeholder="Catatan persetujuan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                        <i class="fas fa-check mr-1"></i> Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL REJECT KANIT ===== --}}
<div class="modal fade" id="modalRejectKanit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-times-circle mr-2"></i> Tolak Logbook
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalRejectKanitForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Admin akan diminta merevisi dan mengajukan ulang.</p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_kanit" class="form-control shadow-none" rows="3"
                                  placeholder="Tuliskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">
                        <i class="fas fa-times mr-1"></i> Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL APPROVE KOORDINATOR ===== --}}
<div class="modal fade" id="modalApproveKoordinator" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-check-double mr-2"></i> Setujui Final
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalApproveKoordinatorForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Logbook akan berstatus <strong>Disetujui Final</strong> dan PDF dapat diunduh.</p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">Catatan (opsional)</label>
                        <textarea name="catatan_koordinator" class="form-control shadow-none" rows="3"
                                  placeholder="Catatan persetujuan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                        <i class="fas fa-check-double mr-1"></i> Ya, Setujui Final
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL REJECT KOORDINATOR ===== --}}
<div class="modal fade" id="modalRejectKoordinator" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-times-circle mr-2"></i> Tolak (Koordinator)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalRejectKoordinatorForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Logbook akan dikembalikan untuk direvisi.</p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_koordinator" class="form-control shadow-none" rows="3"
                                  placeholder="Tuliskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">
                        <i class="fas fa-times mr-1"></i> Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function pemicuEdit(element) {
        document.getElementById('edit_sub_kategori_id').value  = element.getAttribute('data-sub_kategori_id');
        document.getElementById('edit_jenis_logbook').value    = element.getAttribute('data-jenis_logbook');
        document.getElementById('edit_jenis_alat').value       = element.getAttribute('data-jenis_alat');
        document.getElementById('edit_lokasi_tempat').value    = element.getAttribute('data-lokasi_tempat');
        document.getElementById('edit_periode_tersedia').value = element.getAttribute('data-periode_tersedia');
        document.getElementById('formEditLogbook')
            .setAttribute('action', `/logbook/update/${element.getAttribute('data-id')}`);
    }

    function setModalId(formId, action) {
        document.getElementById(formId).setAttribute('action', action);
    }

    $(document).on('click', '.btn-delete', function () {
        $('#delete_nama_logbook').text($(this).data('nama'));
        $('#formDeleteLogbook').attr('action', '/logbook/delete/' + $(this).data('id'));
    });
    function confirmSubmitLogbook(event) {
    event.preventDefault();

    Swal.fire({
        title: 'Ajukan Logbook?',
        text: 'Logbook akan dikirim ke Kepala Unit untuk proses approval.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-paper-plane"></i> Ya, Ajukan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-lg',
            confirmButton: 'px-4',
            cancelButton: 'px-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit();
        }
    });

    return false;
}
</script>
@endpush