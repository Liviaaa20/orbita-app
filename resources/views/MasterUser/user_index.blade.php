@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="font-weight-bold">Data User</h4>
        <button class="btn btn-dark btn-sm" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus mr-1"></i> Tambah User
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
            <table id="tableUser" class="table table-bordered table-striped text-center">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>NIP</th> <th>Nama</th>                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->kode_user }}</td>
                        <td>{{ $user->nip ?? '-' }}</td> <td>{{ $user->name }}</td>                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role->nama_role ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $user->status == 'aktif' ? 'badge-success' : 'badge-danger' }}">
                                {{ $user->status }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <!-- Tombol Edit (Modal) -->
                                <button type="button" class="btn btn-sm btn-light border" data-toggle="modal" data-target="#modalEdit{{ $user->id }}">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>

                                <!-- Tombol Hapus -->
                                <button type="button" class="btn btn-sm btn-light border" 
                                        onclick="if(confirm('Yakin ingin menghapus user ini?')) { document.getElementById('delete-form-{{ $user->id }}').submit(); }">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                                <form id="delete-form-{{ $user->id }}" action="{{ route('user.destroy', $user->id) }}" method="POST" style="display: none;">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('user.update', $user->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-content text-left">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit User</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Nama Lengkap</label>
                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>NIP</label>
                                            <input type="text" name="nip" class="form-control" value="{{ $user->nip }}" maxlength="18">
                                        </div>
                                                                                <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Role</label>
                                            <select name="role_id" class="form-control" required>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                        {{ $role->nama_role }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="aktif" {{ $user->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                <option value="nonaktif" {{ $user->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Password Baru (Kosongkan jika tidak ganti)</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
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
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('user.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Tambah User Baru</h5></div>
                <div class="modal-body">
                    <div class="form-group"><label>ID User</label><input type="text" name="kode_user" class="form-control" required placeholder="U00x"></div>
                    <div class="form-group"><label>Nama Lengkap</label><input type="text" name="name" class="form-control" required></div>
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP" required>
                    </div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role_id" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
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
        // 1. Inisialisasi DataTable dengan ID tableUser
        var table = $('#tableUser').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // Buttons didefinisikan di sini
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });

        // 2. PAKSA render buttons ke dalam container col-md-6 yang pertama (sisi kiri)
        table.buttons().container().appendTo('#tableUser_wrapper .col-md-6:eq(0)');
        
        // 3. Merapikan tampilan search box agar mepet ke kanan
        $('#tableUser_wrapper .dataTables_filter').addClass('text-right');
    });
</script>
@endsection