@extends('layouts.app')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

    @php
        $totalOrganisasi = count($myOrganizations);
        $totalTemplate = $myTemplateCertificate->count();
    @endphp
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6"><div class="card"><div class="text-muted">Organisasi</div><h3>{{$totalOrganisasi}}</h3></div></div>
    <div class="col-md-3 col-6"><div class="card"><div class="text-muted">Template</div><h3>{{$totalTemplate}}</h3></div></div>
</div>

<div class="card">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="mb-0">Organisasi Anda</h5>
        <span>
            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#Join">+ Join Organisasi</a>
        </span>
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
                @forelse($myOrganizations as $idOrganisasi)
                    @php
                        $org = \App\Models\Organization::find($idOrganisasi);
                        $row_member = \App\Models\Member::where('id_Organisasi', $idOrganisasi)
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
                                    <a href="{{ route('organisasi.edit', $org->id) }}"
                                       class="btn btn-sm btn-primary">
                                        Manage
                                    </a>
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
<div class="card">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="mb-0">Sertifikat Terbaru</h5>
        <span>
            <a href="/sertifikat/generate" class="btn btn-success btn-sm">+ Generate Sertifikat</a>
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr><th>No. Sertifikat</th><th>Nama Penerima</th><th>Template</th><th>Organisasi</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($myTemplateCertificate as $template)
                    @php
                        $details = \App\Models\DetailsCertificate::where('id_Sertifikat',$template->id)->get();
                    @endphp
                    @foreach($details as $d)
                        <tr><td>{{$d->NomorSertifikat}}</td><td>{{$d->NamaPenerima}}</td><td>{{$template->Judul}}</td><td>{{$template->organization->Nama}}</td><td>{{$template->is_Premium == 1 ? 'Premium':'Reguler'}}</td></tr>
                    @endforeach
                @empty
                    <p>Tidak ada riwayat pembuatan sertifikat</p>
                @endforelse
            </tbody>
        </table>
    </div>
    
</div>
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
