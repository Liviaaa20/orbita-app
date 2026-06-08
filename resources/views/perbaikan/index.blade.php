@extends('layouts.master')

@push('styles')
<style>
    #tablePerbaikan {
        border-collapse: collapse !important;
        width: 100% !important;
    }

    #tablePerbaikan thead th {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        padding: 10px 12px !important;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    #tablePerbaikan tbody td {
        border: 1px solid #dee2e6 !important;
        padding: 8px 12px !important;
        vertical-align: middle !important;
    }

    .dt-buttons .btn {
        margin-right: 5px;
        border-radius: 4px !important;
        font-size: 13px;
    }

    .dataTables_filter input {
        border-radius: 6px !important;
        border: 1px solid #ced4da !important;
        padding: 4px 10px !important;
    }

    .dataTables_filter {
        text-align: right !important;
    }

    .dataTables_info,
    .dataTables_paginate {
        margin-top: 15px !important;
    }

    .dataTables_paginate .pagination {
        justify-content: flex-end !important;
    }

    .col-wrap {
        white-space: normal !important;
        min-width: 180px !important;
        word-break: break-word;
    }

    .col-status-fixed {
        min-width: 130px !important;
        text-align: center;
    }

    /* FOTO */
   .foto-perbaikan{
    width:45px !important;
    height:45px !important;
    max-width:45px !important;
    max-height:45px !important;
    object-fit:cover !important;
    border-radius:6px !important;
    border:1px solid #dee2e6 !important;
    cursor:pointer;
    display:block !important;
    margin:auto;
    }

    .foto-perbaikan:hover {
        transform: scale(1.08);
    }

    .foto-placeholder {
        width: 45px;
        height: 45px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h3 class="text-uppercase font-weight-bold"
                    style="border-left: 5px solid #003366; padding-left: 15px; margin-bottom: 0;">
                    Permintaan Perbaikan
                </h3>

                @php
                    $roleBisaInput = ['kepala lapangan', 'observer', 'forecaster', 'tata usaha', 'koordinator'];
                    $userRole = strtolower(Auth::user()->role->nama_role ?? '');
                @endphp

                @if(in_array($userRole, $roleBisaInput))
                    <a href="{{ route('perbaikan.create') }}"
                       class="btn btn-primary shadow-sm"
                       style="border-radius: 8px;">
                        <i class="fas fa-plus mr-1"></i> Tambah Permintaan
                    </a>
                @endif
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">

                    <div style="overflow-x: auto; padding: 1rem;">

                        <table id="tablePerbaikan"
                               class="table table-bordered mb-0"
                               style="width:100%">

                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:5%">Foto</th>
                                    <th>Tanggal Permintaan</th>
                                    <th>Tanggal Diterima</th>
                                    <th>Tanggal Selesai</th>
                                    <th>User</th>
                                    <th>Kategori</th>
                                    <th class="col-wrap">Keterangan</th>
                                    <th style="width:10%">Validasi</th>
                                    <th class="col-wrap">Catatan</th>
                                    <th class="col-status-fixed">Status</th>
                                    <th style="width:10%">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($perbaikans as $p)
                                <tr>
                                 
                                {{-- MODAL FOTO --}}
@if($p->foto)
<div class="modal fade"
     id="modalFoto{{ $p->id }}"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    Foto Tiket #{{ $p->no_tiket }}
                </h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body text-center p-2">

                <img src="{{ asset('storage/' . $p->foto) }}"
                     class="img-fluid rounded"
                     style="
                        max-height:80vh;
                        object-fit:contain;
                     ">

            </div>

        </div>

    </div>

</div>
@endif

                                    <td class="text-center font-weight-bold">
                                        {{ $p->no_tiket }}
                                    </td>

<td class="text-center align-middle">

    @if($p->foto)

        <img src="{{ asset('storage/' . $p->foto) }}"
             alt="Foto Perbaikan"
             style="
                width:45px !important;
                height:45px !important;
                max-width:45px !important;
                max-height:45px !important;
                object-fit:cover !important;
                border-radius:6px;
                border:1px solid #dee2e6;
                display:block;
                margin:auto;
                cursor:pointer;
             "
             data-toggle="modal"
             data-target="#modalFoto{{ $p->id }}"
             title="Klik untuk perbesar">

    @else

        <span class="text-muted small">-</span>

    @endif

</td>                                    <td class="text-center">
                                        {{ $p->tgl_permintaan }}
                                    </td>

                                    <td class="text-center font-weight-bold text-success">
                                        {{ $p->tgl_diterima ?? '-' }}
                                    </td>

                                    <td class="text-center font-weight-bold text-info">
                                        {{ $p->tgl_selesai ?? '-' }}
                                    </td>

                                    <td>{{ $p->user }}</td>

                                    <td>{{ $p->kategori_perbaikan }}</td>

                                    <td class="col-wrap">
                                        {{ $p->keterangan }}
                                    </td>

                                    {{-- VALIDASI --}}
                                    <td class="text-center" style="min-width:100px;">

                                        @if(Auth::user()->role && (Auth::user()->role->nama_role == 'admin' || Auth::user()->role->nama_role == 'teknisi'))

                                            @if(!$p->tgl_diterima)

                                                <form action="{{ route('perbaikan.validasi', $p->id) }}"
                                                      method="POST">

                                                    @csrf

                                                    <div class="btn-group">

                                                        <button type="submit"
                                                                name="action"
                                                                value="terima"
                                                                class="btn btn-sm btn-success shadow-sm"
                                                                title="Terima">
                                                            <i class="fas fa-check"></i>
                                                        </button>

                                                        <button type="submit"
                                                                name="action"
                                                                value="tolak"
                                                                class="btn btn-sm btn-danger shadow-sm"
                                                                title="Tolak">
                                                            <i class="fas fa-times"></i>
                                                        </button>

                                                    </div>

                                                </form>

                                            @else

                                                <span class="badge badge-pill badge-success px-2 py-1">
                                                    <i class="fas fa-check-circle"></i> Valid
                                                </span>

                                            @endif

                                        @else

                                            {!! $p->tgl_diterima
                                                ? '<span class="text-success small font-weight-bold">DITERIMA</span>'
                                                : '<span class="text-muted small">MENUNGGU</span>' !!}

                                        @endif

                                    </td>

                                    <td class="col-wrap small text-muted">
                                        {{ $p->catatan ?? '-' }}
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="text-center col-status-fixed">

                                        @if(Auth::user()->role && (Auth::user()->role->nama_role == 'admin' || Auth::user()->role->nama_role == 'teknisi'))

                                            <form action="{{ route('perbaikan.update', $p->id) }}"
                                                  method="POST">

                                                @csrf
                                                @method('PUT')

                                                <select name="status"
                                                        class="form-control form-control-sm border-0 font-weight-bold {{ $p->status == 'selesai' ? 'text-success' : 'text-warning' }}"
                                                        onchange="this.form.submit()"
                                                        style="cursor:pointer;">

                                                    <option value="onproses"
                                                        {{ $p->status == 'onproses' ? 'selected' : '' }}>
                                                        ● On Proses
                                                    </option>

                                                    <option value="selesai"
                                                        {{ $p->status == 'selesai' ? 'selected' : '' }}>
                                                        ● Selesai
                                                    </option>

                                                </select>

                                                <input type="hidden"
                                                       name="catatan"
                                                       value="{{ $p->catatan }}">

                                            </form>

                                        @else

                                            <span class="badge {{ $p->status == 'selesai' ? 'badge-success' : 'badge-warning' }}">
                                                {{ $p->status == 'selesai' ? '● Selesai' : '○ On Proses' }}
                                            </span>

                                        @endif

                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center">

                                        <div class="btn-group" role="group">

                                            <a href="{{ route('perbaikan.download', $p->id) }}"
                                               class="btn btn-sm btn-light border shadow-sm"
                                               title="Download">
                                                <i class="fas fa-download text-primary"></i>
                                            </a>

                                            @if(Auth::user()->role && in_array(Auth::user()->role->nama_role, ['admin', 'teknisi']))

                                                <button type="button"
                                                        class="btn btn-sm btn-light border shadow-sm"
                                                        data-toggle="modal"
                                                        data-target="#modalCatatan{{ $p->id }}"
                                                        title="Edit Catatan">
                                                    <i class="fas fa-edit text-warning"></i>
                                                </button>

                                            @endif

                                        </div>

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
@foreach($perbaikans as $p)

<!-- Modal Edit Catatan -->
<div class="modal fade" id="modalCatatan{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('perbaikan.update', $p->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        Edit Perbaikan #{{ $p->no_tiket }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="onproses"
                                {{ $p->status == 'onproses' ? 'selected' : '' }}>
                                On Proses
                            </option>

                            <option value="selesai"
                                {{ $p->status == 'selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="catatan"
                                  rows="4"
                                  class="form-control">{{ $p->catatan }}</textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endforeach
@endsection