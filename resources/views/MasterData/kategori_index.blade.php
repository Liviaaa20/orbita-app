@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold text-dark">Data Kategori</h5>
                        <button class="btn btn-dark btn-sm px-3" style="border-radius: 8px;" data-toggle="modal" data-target="#modalTambahKategori">
                            <i class="fas fa-plus mr-1"></i> Tambah Kategori
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-kategori" class="table table-bordered table-hover mt-2">
                        <thead style="background-color: #f2f2f2;">
                            <tr class="text-center">
                                <th width="80px">ID</th>
                                <th>Kategori</th>
                                <th>Jenis</th>
                                <th width="120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategori as $item)
                            <tr class="text-center">
                                <td>{{ $item->kode_kategori }}</td>
                                <td class="text-left">{{ $item->nama_kategori }}</td>
                                <td>
                                    <span class="badge {{ $item->jenis == 'Sistem' ? 'badge-primary' : 'badge-secondary' }} px-2 py-1">
                                        {{ $item->jenis }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm text-secondary btn-view" 
                                            data-toggle="modal" 
                                            data-target="#modalDetailKategori"
                                            data-kode="{{ $item->kode_kategori }}"
                                            data-nama="{{ $item->nama_kategori }}"
                                            data-jenis="{{ $item->jenis }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <button class="btn btn-sm text-secondary btn-edit" 
                                            data-toggle="modal" 
                                            data-target="#modalEditKategori"
                                            data-id="{{ $item->id }}"
                                            data-kode="{{ $item->kode_kategori }}"
                                            data-nama="{{ $item->nama_kategori }}"
                                            data-jenis="{{ $item->jenis }}">
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <form action="{{ route('kategori.destroy', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm text-secondary" onclick="return confirm('Hapus data?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">Form Tambah Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Kategori</label>
                        <input type="text" name="kode_kategori" class="form-control" placeholder="K001" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis</label>
                        <select name="jenis" class="form-control" required>
                            <option value="Sistem">Sistem</option>
                            <option value="Non Sistem">Non Sistem</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetailKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">Detail Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr><th width="150">Kode</th><td id="view_kode"></td></tr>
                    <tr><th>Nama Kategori</th><td id="view_nama"></td></tr>
                    <tr><th>Jenis</th><td><span id="view_jenis"></span></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEditKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">Edit Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEditKategori" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode</label>
                        <input type="text" id="edit_kode" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis</label>
                        <select name="jenis" id="edit_jenis" class="form-control">
                            <option value="Sistem">Sistem</option>
                            <option value="Non Sistem">Non Sistem</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#table-kategori')) {
            var table = $('#table-kategori').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "language": {
                    "search": "Search:",
                    "paginate": {
                        "previous": "Previous",
                        "next": "Next"
                    }
                }
            });
            table.buttons().container().appendTo('#table-kategori_wrapper .col-md-6:eq(0)');
            $('#table-kategori_wrapper .col-md-6:eq(1)').addClass('text-right');
            $('#table-kategori_wrapper .row:eq(2) .col-md-7').addClass('d-flex justify-content-end');
        }

        $(document).on('click', '.btn-view', function() {
            var kode  = $(this).attr('data-kode');
            var nama  = $(this).attr('data-nama');
            var jenis = $(this).attr('data-jenis');

            $('#view_kode').text(kode);
            $('#view_nama').text(nama);
            $('#view_jenis').text(jenis);
            
            var badgeClass = (jenis === 'Sistem') ? 'badge badge-primary px-2 py-1' : 'badge badge-secondary px-2 py-1';
            $('#view_jenis').attr('class', badgeClass);
        });

        $(document).on('click', '.btn-edit', function() {
            var id    = $(this).attr('data-id');
            var kode  = $(this).attr('data-kode');
            var nama  = $(this).attr('data-nama');
            var jenis = $(this).attr('data-jenis');

            $('#edit_kode').val(kode);
            $('#edit_nama').val(nama);
            $('#edit_jenis').val(jenis);
            
            $('#formEditKategori').attr('action', '/kategori/' + id);
        });
    });
</script>
@endpush