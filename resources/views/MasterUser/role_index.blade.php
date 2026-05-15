@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-weight-bold">Data Role</h4>
        <button class="btn btn-dark btn-sm" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus mr-1"></i> Tambah Role
        </button>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

    <div class="card">
        <div class="card-body">
            <table id="tableRole" class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->kode_role }}</td>
                        <td>{{ $role->nama_role }}</td>
                        <td>
                            <button class="btn btn-sm btn-light border" data-toggle="modal" data-target="#modalEdit{{ $role->id }}">
                                <i class="fas fa-edit text-primary"></i>
                            </button>
                            <form action="{{ route('role.destroy', $role->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light border" onclick="return confirm('Hapus role ini?')">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit{{ $role->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('role.update', $role->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header"><h5>Edit Role</h5></div>
                                    <div class="modal-body text-left">
                                        <div class="form-group">
                                            <label>Kode Role</label>
                                            <input type="text" name="kode_role" class="form-control" value="{{ $role->kode_role }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Role</label>
                                            <input type="text" name="nama_role" class="form-control" value="{{ $role->nama_role }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('role.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Tambah Role Baru</h5></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Role (Contoh: R003)</label>
                        <input type="text" name="kode_role" class="form-control" placeholder="Rxxx" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Role</label>
                        <input type="text" name="nama_role" class="form-control" placeholder="Nama Role" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        // 1. Inisialisasi DataTable dengan ID tableRole
        var table = $('#tableRole').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // Buttons didefinisikan di sini agar sistem siap merender
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });

        // 2. PAKSA render buttons ke dalam container col-md-6 yang pertama (sisi kiri)
        table.buttons().container().appendTo('#tableRole_wrapper .col-md-6:eq(0)');
        
        // 3. Merapikan tampilan search box agar mepet ke kanan
        $('#tableRole_wrapper .dataTables_filter').addClass('text-right');
    });
</script>
@endsection