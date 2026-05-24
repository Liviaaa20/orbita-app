@extends('layouts.master')
<style>
    /* CSS BAWAAN KAMU (DIPERTAHANKAN & DIPERBAIKI) */
    .table-responsive { overflow-x: auto !important; border: none !important; }
    
    /* KUNCI: Menghilangkan celah antara header dan body DataTables */
    .dataTables_scrollHead { margin-bottom: -2px !important; } 
    .dataTables_scrollBody { border-top: none !important; }
    
    #tablePerbaikan {
        border-collapse: collapse !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }

    /* Header abu-abu bersih */
    #tablePerbaikan thead th {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        padding: 12px !important;
        vertical-align: middle;
    }

    /* Isi tabel */
    #tablePerbaikan tbody td {
        border: 1px solid #dee2e6 !important;
        padding: 10px !important;
        vertical-align: middle !important;
    }

    /* Atur Jarak Search & Paginate */
    .dataTables_filter { text-align: right !important; }
    .dataTables_filter label { font-weight: bold; }
    
    /* Kasih napas buat paginate agar lebih turun */
    .dataTables_info, .dataTables_paginate {
        margin-top: 25px !important;
        padding-top: 10px;
    }
    
    /* Tombol Paginate lurus kanan */
    .dataTables_paginate .pagination {
        justify-content: flex-end !important;
    }
    
    .img-custom { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
    /* Scrollbar Styling */
    .dataTables_scrollBody::-webkit-scrollbar { height: 8px; }
    .dataTables_scrollBody::-webkit-scrollbar-thumb { background: #ced4da; border-radius: 10px; }

    /* Utilitas Kolom */
    .col-status-fixed { min-width: 130px !important; text-align: center; }
    .col-wrap { white-space: normal !important; min-width: 200px !important; word-break: break-word; }

    /* Merapikan Buttons & Search agar sejajar */
    .dt-buttons .btn { margin-right: 5px; border-radius: 4px !important; font-size: 13px; }
    .dataTables_filter input { border-radius: 6px !important; border: 1px solid #ced4da !important; padding: 4px 10px !important; }
</style>

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h3 class="text-uppercase font-weight-bold" style="border-left: 5px solid #003366; padding-left: 15px; margin-bottom: 0;">
                    Permintaan Perbaikan
                </h3>

                {{-- Role yang diizinkan untuk menambah data --}}
                @php
                    $roleBisaInput = ['teknisi', 'kepala unit', 'observer', 'forcaster', 'Tata Usaha'];
                    $userRole = Auth::user()->role->nama_role ?? '';
                @endphp

                @if(in_array($userRole, $roleBisaInput)) 
                    <a href="{{ route('perbaikan.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-plus mr-1"></i> Tambah Permintaan
                    </a>
                @endif
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive border-0">
                        <table id="tablePerbaikan" class="table table-bordered align-middle w-100" style="border-bottom: none !important;">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 10%">Foto</th>
                                    <th>Tanggal Permintaan</th>
                                    <th>Tanggal Diterima</th>
                                    <th>Tanggal Selesai</th>
                                    <th>User</th>
                                    <th>Kategori</th>
                                    <th class="col-wrap">Keterangan</th>
                                    <th style="width: 10%">Validasi</th>
                                    <th class="col-wrap">Catatan</th>
                                    <th class="col-status-fixed">Status</th>
                                    <th style="width: 10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($perbaikans as $p)
                                <tr>
                                    <td class="text-center font-weight-bold">{{ $p->no_tiket }}</td>
                                    <td class="text-center">
                                        @if($p->foto)
                                            <img src="{{ asset('storage/'.$p->foto) }}" width="80" class="img-thumbnail">
                                        @else
                                            <span class="text-muted small italic">No Photo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $p->tgl_permintaan }}</td>
                                    
                                    {{-- TANGGAL DITERIMA (Hasil dari Validasi) --}}
                                    <td class="text-center font-weight-bold text-success">
                                        {{ $p->tgl_diterima ?? '-' }}
                                    </td>

                                    {{-- TANGGAL SELESAI (Hasil dari Status Selesai) --}}
                                    <td class="text-center font-weight-bold text-info">
                                        {{ $p->tgl_selesai ?? '-' }}
                                    </td>

                                    <td>{{ $p->user }}</td>
                                    <td>{{ $p->kategori_perbaikan }}</td>
                                    <td class="col-wrap">{{ $p->keterangan }}</td>
                                    
                                    {{-- LOGIKA VALIDASI (Tombol Centang/Silang) --}}
                                    <td class="text-center" style="min-width: 100px;">
                                        @if(Auth::user()->role && Auth::user()->role->nama_role == 'admin')
                                            @if(!$p->tgl_diterima)
                                                {{-- Jika belum divalidasi, munculkan tombol --}}
                                                <form action="{{ route('perbaikan.validasi', $p->id) }}" method="POST">
                                                    @csrf
                                                    <div class="btn-group">
                                                        <button type="submit" name="action" value="terima" class="btn btn-sm btn-success shadow-sm" title="Terima">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="submit" name="action" value="tolak" class="btn btn-sm btn-danger shadow-sm" title="Tolak">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            @else
                                                {{-- Jika sudah divalidasi --}}
                                                <span class="badge badge-pill badge-success px-2 py-1"><i class="fas fa-check-circle"></i> Valid</span>
                                            @endif
                                        @else
                                            {{-- Tampilan untuk Non-Admin --}}
                                            {!! $p->tgl_diterima ? '<span class="text-success small font-weight-bold">DITERIMA</span>' : '<span class="text-muted small">MENUNGGU</span>' !!}
                                        @endif
                                    </td>

                                    {{-- KOLOM CATATAN --}}
                                    <td class="col-wrap small text-muted">
                                        {{ $p->catatan ?? '-' }}
                                    </td>
                                    
                                    {{-- LOGIKA STATUS (On Proses / Selesai) --}}
                                    <td class="text-center col-status-fixed">
                                        @if(Auth::user()->role && Auth::user()->role->nama_role == 'admin')
                                            <form action="{{ route('perbaikan.update', $p->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" class="form-control form-control-sm border-0 font-weight-bold {{ $p->status == 'selesai' ? 'text-success' : 'text-warning' }}" 
                                                        onchange="this.form.submit()" style="cursor: pointer;">
                                                    <option value="onproses" {{ $p->status == 'onproses' ? 'selected' : '' }}>● On Proses</option>
                                                    <option value="selesai" {{ $p->status == 'selesai' ? 'selected' : '' }}>● Selesai</option>
                                                </select>
                                                {{-- Input hidden menjaga catatan saat status diubah via select ini --}}
                                                <input type="hidden" name="catatan" value="{{ $p->catatan }}">
                                            </form>
                                        @else
                                            <span class="badge {{ $p->status == 'selesai' ? 'badge-success' : 'badge-warning' }}">
                                                {{ $p->status == 'selesai' ? '● Selesai' : '○ On Proses' }}
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- KOLOM AKSI --}}
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            {{-- Tombol Download --}}
                                            <a href="{{ route('perbaikan.download', $p->id) }}" class="btn btn-sm btn-light border shadow-sm" title="Download">
                                                <i class="fas fa-download text-primary"></i>
                                            </a>
                                            
                                            {{-- Tombol Edit Catatan (Hanya Muncul untuk Admin) --}}
                                            @if(Auth::user()->role && Auth::user()->role->nama_role == 'admin')
                                                <button type="button" class="btn btn-sm btn-light border shadow-sm" 
                                                        data-toggle="modal" data-target="#modalCatatan{{ $p->id }}" title="Edit Catatan">
                                                    <i class="fas fa-edit text-warning"></i>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- MODAL INTERAKTIF KHUSUS EDIT CATATAN --}}
                                        @if(Auth::user()->role && Auth::user()->role->nama_role == 'admin')
                                            <div class="modal fade" id="modalCatatan{{ $p->id }}" tabindex="-1" role="dialog" aria-labelledby="modalCatatanLabel{{ $p->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title font-weight-bold" id="modalCatatanLabel{{ $p->id }}">
                                                                <i class="fas fa-comment-alt text-warning mr-2"></i> Edit Catatan Tiket #{{ $p->no_tiket }}
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('perbaikan.update', $p->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                {{-- Mengunci status saat ini agar tidak ikut berubah --}}
                                                                <input type="hidden" name="status" value="{{ $p->status }}">
                                                                
                                                                <div class="form-group">
                                                                    <label class="font-weight-bold text-dark">Catatan Perbaikan :</label>
                                                                    <textarea name="catatan" class="form-control" rows="5" placeholder="Masukkan catatan atau progress perbaikan di sini...">{{ $p->catatan }}</textarea>
                                                                    <small class="form-text text-muted">Catatan ini langsung disimpan tanpa mengubah status perbaikan saat ini.</small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-whitesmoke">
                                                                <button type="button" class="btn btn-light border" data-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary shadow-sm">
                                                                    <i class="fas fa-save mr-1"></i> Simpan Catatan
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#tablePerbaikan')) {
            var table = $('#tablePerbaikan').DataTable({
                "responsive": false, 
                "scrollX": true, 
                "scrollCollapse": true,
                "lengthChange": false, 
                "autoWidth": false,
                "ordering": false,
                "dom": "<'row'<'col-md-6'B><'col-md-6'f>>" +
                       "<'row'<'col-12'tr>>" +
                       "<'row'<'col-md-5 mt-3'i><'col-md-7 mt-3'p>>",
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "language": {
                    "search": "Cari Data:",
                    "paginate": { "previous": "Sebelumnya", "next": "Selanjutnya" },
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
                }
            });

            // Sinkronisasi posisi search
            $('#tablePerbaikan_filter').addClass('text-right');
            
            // Jarak tombol ekspor dataTables
            table.buttons().container().css('margin-bottom', '10px');

            // Perbaikan auto-width scrollX DataTables
            setTimeout(function() {
                table.columns.adjust().draw();
            }, 300);

            $(window).on('resize', function () {
                table.columns.adjust();
            });
        }
    });
</script>
@endpush