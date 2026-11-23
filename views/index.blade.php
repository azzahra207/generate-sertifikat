@extends('layouts.app')
@section('page-title', 'Daftar Organisasi')
@section('breadcrumb', 'Organisasi / Daftar')

@section('content')
<div class="card">
    <div class="d-flex gap-2">
    <form action="{{ route('organisasi.index') }}" method="GET" class="d-flex align-items-center gap-2">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama organisasi..." value="{{ $search }}">
        <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">Cari</button>
        @if($search)
            <a href="{{ route('organisasi.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Pencarian">
                Reset
            </a>
        @endif
    </form>
    <a href="#" class="btn btn-primary btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#Join">+ Join Organisasi</a>
</div>
</div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr><th>No</th><th>Logo</th><th>Nama Instansi</th><th>Jabatan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @php 
                    $no = 1; 
                @endphp
                @forelse($myOrganizations as $org)
                    @php
                        $row_member = \App\Models\Member::where('id_Organisasi', $org->id)
                            ->where('id_User', auth()->id())
                            ->first();
                    @endphp

                    @if($org)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>
                                @if($org->Logo)
                                    <img src="{{ asset('storage/' . $org->Logo) }}" alt="Logo {{ $org->Nama }}" width="48" height="48" class="rounded object-fit-contain">
                                @else
                                    <div class="d-flex align-items-center justify-content-center rounded bg-secondary text-white" style="width:48px;height:48px;font-weight:600;"> {{ strtoupper(substr($org->Nama, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $org->Nama }}</td>
                            <td>{{ $row_member->role ?? '-' }}</td>
                            <td>
                                @if($row_member && $row_member->role === 'Owner')
                                    <button type="button" class="btn btn-sm btn-primary btn-manage"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit"
                                            data-id="{{ $org->id }}"
                                            data-nama="{{ $org->Nama }}"
                                            data-kode="{{ $org->Kode }}"
                                            data-logo="{{ $org->Logo ? asset('storage/' . $org->Logo) : '' }}">
                                        Manage
                                    </button>
                                @else
                                <form action="{{route('organisasi.keluar',['idOrg' => $org->id,'idUser' => auth()->user()->id])}}" method="POST" class="d-inline" onsubmit="return confirm('Keluar dari organisasi ini?')">
                                    @csrf 
                                    <button class="btn btn-sm btn-danger">Keluar</button>
                                </form>
                                @endif
                                
                               <button type="button" class="btn btn-sm btn-outline-secondary btn-copy-kode" data-kode="{{ $org->Kode }}" title="Salin kode organisasi">
                                    <i class="bi bi-clipboard"></i> Copy Kode
                                </button>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Tidak ada Organisasi yang Anda ikuti
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
</div>

<!-- Modal Join -->
<div class="modal fade" id="Join" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            {{-- Tabs --}}
            <div class="modal-header d-block pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="modal-title mb-0">Organisasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <ul class="nav nav-tabs mt-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-join-btn"
                                data-bs-toggle="tab" data-bs-target="#tab-join"
                                type="button" role="tab">
                            Join Organisasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-create-btn"
                                data-bs-toggle="tab" data-bs-target="#tab-create"
                                type="button" role="tab">
                            Buat Baru
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content">

                <div class="tab-pane fade show active" id="tab-join" role="tabpanel">
                    <form id="form-join" action="{{ route('organisasi.join') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Kode Organisasi</label>
                                <div class="position-relative">
                                    <input placeholder="*********" id="kode" type="password"
                                           class="form-control" name="kode" required
                                           autocomplete="one-time-code">
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                                          style="cursor: pointer;" onclick="togglekode()">
                                        <i id="eye-icon" class="bi bi-eye text-secondary fs-5"></i>
                                    </span>
                                </div>
                                <div id="kode-error" class="invalid-feedback d-block"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="btn-join">Join</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="tab-create" role="tabpanel">
                    <form id="form-create" action="{{ route('organisasi.store') }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Kode Organisasi</label>
                                <div class="input-group">
                                    <input type="text" name="Kode" id="create-kode" class="form-control font-monospace @error('Kode') is-invalid @enderror" readonly value="{{ old('Kode') }}">
                                        @error('Kode')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    <button type="button" class="btn btn-outline-secondary"
                                            id="btn-copy-kode" title="Salin kode">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary"
                                            id="btn-regen-kode" title="Buat ulang kode">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                                <small class="text-muted">
                                    Kode dibuat otomatis. Salin dan simpan — bagikan ke orang yang ingin bergabung.
                                </small>
                                <div class="invalid-feedback d-block" id="create-kode-error"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Organisasi</label>
                                <input type="text" name="Nama" id="create-nama" class="form-control @error('Nama') is-invalid @enderror" required maxlength="100" value="{{ old('Nama') }}">
                                @error('Nama')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="create-nama-error"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Logo <span class="text-muted">(opsional)</span></label>
                                <input type="file" name="Logo" id="create-logo" class="form-control @error('Logo') is-invalid @enderror" accept="image/*">
                                @error('Logo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: JPG, PNG, WEBP. Maks 2MB.</small>
                                <div class="invalid-feedback" id="create-logo-error"></div>
                            </div>
                        
                            <div class="alert alert-info small mb-0">
                                <i class="bi bi-info-circle"></i>
                                Anda akan menjadi <strong>Owner</strong> organisasi ini.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="btn-create">Buat Organisasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Edit Organisasi -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEdit" action="{{ route('organisasi.update', ['id' => '__ID__']) }}" data-action-template="{{ route('organisasi.update', ['id' => '__ID__']) }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title">Edit Organisasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- Preview Logo --}}
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div id="edit-logo-preview-wrap">
                        <img id="edit-logo-preview" src="" alt="Logo"
                             width="64" height="64"
                             class="rounded object-fit-contain border d-none">
                        <div id="edit-logo-placeholder"
                             class="d-flex align-items-center justify-content-center rounded bg-secondary text-white"
                             style="width:64px;height:64px;font-weight:600;">?</div>
                    </div>
                    <div class="flex-grow-1">
                        <label class="form-label mb-1">Logo <span class="text-muted">(opsional)</span></label>
                        <input type="file" name="Logo" id="edit-logo"
                               class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Organisasi</label>
                    <input type="text" name="Nama" id="edit-nama"
                           class="form-control" required maxlength="100">
                </div>

                <div class="mb-3">
                    <label class="form-label">Kode Organisasi</label>
                    <div class="input-group">
                        <input type="text" name="Kode" id="edit-kode"
                               class="form-control font-monospace" readonly>
                        <button type="button" class="btn btn-outline-primary"
                                id="btn-regen-kode-edit" title="Buat ulang kode">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    <small class="text-muted">
                        Kode dibuat otomatis. Regenerate akan membuat kode baru — kode lama tidak berlaku lagi.
                    </small>
                    <div class="invalid-feedback d-block" id="edit-kode-error"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<script>
    function togglekode() {
        const input = document.getElementById('kode');
        const icon  = document.getElementById('eye-icon');
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // ===== Generator kode =====
    function generateKode(length = 15) {
        const alphabet = 'abcdefghjkmnopqrstuvwxyz23456789';
        let kode = '';
        const buf = new Uint32Array(length);
        crypto.getRandomValues(buf);
        for (let i = 0; i < length; i++) {
            kode += alphabet[buf[i] % alphabet.length];
        }
        return kode;
    }

    // ===== Modal: isi kode saat dibuka =====
    const modalJoin = document.getElementById('Join');
    const inputKodeCreate = document.getElementById('create-kode');

    modalJoin?.addEventListener('show.bs.modal', function () {
        if (inputKodeCreate && !inputKodeCreate.value) {
            inputKodeCreate.value = generateKode();
        }
    });

    // ===== Modal: tombol regenerate =====
    document.getElementById('btn-regen-kode')?.addEventListener('click', function () {
        if (inputKodeCreate) inputKodeCreate.value = generateKode();
    });

    // ===== Modal: tombol copy (ID unik) =====
    document.getElementById('btn-copy-kode-modal')?.addEventListener('click', async function () {
        const kode = inputKodeCreate?.value;
        if (!kode) return;

        try {
            await navigator.clipboard.writeText(kode);
            const icon = this.querySelector('i');
            icon.classList.remove('bi-clipboard');
            icon.classList.add('bi-check-lg', 'text-success');
            setTimeout(() => {
                icon.classList.remove('bi-check-lg', 'text-success');
                icon.classList.add('bi-clipboard');
            }, 1500);
        } catch (err) {
            inputKodeCreate.select();
            document.execCommand('copy');
            alert('Kode disalin: ' + kode);
        }
    });

    // ===== Tabel: tombol copy per baris (class) =====
    document.querySelectorAll('.btn-copy-kode').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const kode = this.dataset.kode;
            if (!kode) return;

            try {
                await navigator.clipboard.writeText(kode);
                const original = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check-lg"></i> Tersalin';
                this.classList.add('btn-success');
                this.classList.remove('btn-outline-secondary');
                setTimeout(() => {
                    this.innerHTML = original;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-secondary');
                }, 1500);
            } catch (err) {
                const ta = document.createElement('textarea');
                ta.value = kode;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                try {
                    document.execCommand('copy');
                    alert('Kode disalin: ' + kode);
                } catch (e) {
                    alert('Gagal menyalin. Kode: ' + kode);
                }
                document.body.removeChild(ta);
            }
        });
    });    
    document.getElementById('btn-regen-kode-edit')?.addEventListener('click', function () {
    if (!confirm('Buat ulang kode? Kode lama tidak akan berlaku lagi.')) return;
    const input = document.getElementById('edit-kode');
    if (input) input.value = generateKode();
});
const modalEdit = document.getElementById('modalEdit');
modalEdit?.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    if (!btn) return;

    const id   = btn.getAttribute('data-id');
    const nama = btn.getAttribute('data-nama') || '';
    const kode = btn.getAttribute('data-kode') || '';
    const logo = btn.getAttribute('data-logo');

    // Isi field dengan data lama
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-kode').value = kode;
    document.getElementById('edit-kode-error').textContent = '';

    // Patch action dari template (idempoten, bisa dipanggil berkali-kali)
    const form = document.getElementById('formEdit');
    form.action = form.dataset.actionTemplate.replace('__ID__', id);

    // Preview logo
    const img = document.getElementById('edit-logo-preview');
    const ph  = document.getElementById('edit-logo-placeholder');
    if (logo) {
        img.src = logo;
        img.classList.remove('d-none');
        ph.classList.add('d-none');
    } else {
        img.classList.add('d-none');
        ph.classList.remove('d-none');
        ph.textContent = (nama || '?').charAt(0).toUpperCase();
    }

    // Reset file input supaya tidak bawa file dari sesi sebelumnya
    document.getElementById('edit-logo').value = '';
});
</script>
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Buka modal Join
        var myModal = new bootstrap.Modal(document.getElementById('Join'));
        myModal.show();

        // Pindah ke tab "Buat Baru"
        var triggerTab = document.getElementById('tab-create-btn');
        var tab = new bootstrap.Tab(triggerTab);
        tab.show();
    });
</script>
@endif
@endsection