@extends('layouts.app')
@section('page-title', 'Member')
@section('breadcrumb', 'Organisasi / Member')

@section('content')
<div class="card">
    <div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
        <div class="d-flex gap-2">
           <form action="{{ route('organisasi.member') }}" method="GET" class="d-flex flex-wrap gap-2">
                <select name="organisasi_id" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="all">Semua Organisasi</option>
                    @foreach($organizations as $o) 
                        <option value="{{ $o->id }}" {{ $organisasiId == $o->id ? 'selected' : '' }}>
                            {{ $o->Nama }}
                        </option>
                    @endforeach
                </select>

                <input type="text" name="search" class="form-control w-auto" placeholder="Cari nama / email..." value="{{ $search }}">
                
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search"></i> Cari
                </button>
                
                @if($search || ($organisasiId && $organisasiId !== 'all'))
                    <a href="{{ route('organisasi.member') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                @endif
            </form>
        </div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Member</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr><th>No</th><th>Nama</th><th>Email</th><th>Organisasi</th><th>Role</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($members as $index => $m)
                <tr>
                    <td>{{ $members->firstItem() + $index }}</td>
                    <td>{{ $m->user->name ?? '-' }}</td>
                    <td>{{ $m->user->email ?? '-' }}</td>
                    <td>{{ $m->organization->Nama ?? '-' }}</td>
                    <td><span class="badge bg-success">{{ $m->role }}</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning btn-ubah-role" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalUbahRole"
                                data-id="{{ $m->id }}"
                                data-nama="{{ $m->user->name ?? '-' }}"
                                data-role="{{ $m->role }}">
                            Ubah Role
                        </button>
                        
                        <form action="{{route('organisasi.keluar',['idOrg' => $m->id_Organisasi,'idUser' => $m->id_User])}}" method="POST" class="d-inline" onsubmit="return confirm('Keluarkan dari organisasi?')">
                            @csrf 
                            <button class="btn btn-sm btn-danger">Keluarkan</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        @if($search)
                            Member dengan nama/email "{{ $search }}" tidak ditemukan.
                        @else
                            Belum ada data member.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- TAMBAHKAN PAGINATION DI SINI --}}
    @if($members->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            Menampilkan {{ $members->firstItem() }} - {{ $members->lastItem() }} dari {{ $members->total() }} anggota
        </div>
        <div>
            {{ $members->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>
<!-- Modal Ubah Role -->
<div class="modal fade" id="modalUbahRole" tabindex="-1">
    <div class="modal-dialog">
    <form id="formUbahRole" action="{{ route('organisasi.ubah.role', ['id' => '__ID__']) }}" method="POST" class="modal-content">            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Ubah Role Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Ubah role untuk: <strong id="ubah-nama"></strong></p>
                <div class="mb-3">
                    <label class="form-label">Role Baru</label>
                    <select name="role" id="ubah-role-select" class="form-select" required>
                        <option value="Admin">Admin</option>
                        <option value="Owner">Owner</option>
                    </select>
                </div>
                <!-- Peringatan Transfer Kepemilikan -->
                <div id="warning-transfer" class="alert alert-warning d-none mb-0 small">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Mengangkat anggota ini sebagai Owner akan <strong>menurunkan jabatan Anda menjadi Admin</strong>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
        document.getElementById('modalUbahRole').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const memberId = button.getAttribute('data-id');
        const form = document.getElementById('formUbahRole');
        form.action = form.action.replace('__ID__', memberId);
    });
    document.getElementById('ubah-role-select').addEventListener('change', function() {
    const warningBox = document.getElementById('warning-transfer');
    if (this.value === 'Owner') {
        warningBox.classList.remove('d-none');
    } else {
        warningBox.classList.add('d-none');
    }
});
</script>
@endpush
@endsection