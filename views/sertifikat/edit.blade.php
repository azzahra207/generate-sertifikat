@extends('layouts.app')
@section('page-title', 'Editor Sertifikat')
@section('breadcrumb', 'Sertifikat / Editor')
@section('editor', '1') {{-- tampil layar penuh di bawah menu atas, tanpa judul halaman --}}

@push('styles')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pinyon+Script&family=Dancing+Script:wght@400;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Lora:ital,wght@0,400;0,700;1,400;1,700&family=Cinzel:wght@400;700&family=Montserrat:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:ital,wght@0,400;0,700;1,400;1,700&display=swap">
<style>
html, body { height: 100%; }
body { display: flex; flex-direction: column; overflow: hidden; font-size: 14px; }
.ed-bar { height: 56px; flex: none; background: var(--sk-surface); }
#pname { border-color: transparent; font-weight: 600; max-width: 320px; }
#pname:hover, #pname:focus { border-color: var(--sk-line); }
.ed-body { flex: 1; display: flex; min-height: 0; }
.ed-left { width: 320px; flex: none; border-right: 1px solid var(--sk-line); display: flex; flex-direction: column; background: var(--sk-surface); min-height: 0; }
.ed-left .nav-tabs .nav-link { border: 0; border-bottom: 2px solid transparent; color: var(--bs-secondary-color); font-weight: 600; border-radius: 0; }
.ed-left .nav-tabs .nav-link.active { color: var(--sk-primary); border-bottom-color: var(--sk-primary); }
.ed-pane { flex: 1; overflow: auto; padding: 16px; }
.fi { cursor: pointer; min-width: 0; }
.fi .text-truncate { min-width: 0; }
.fi > span:last-child { white-space: nowrap; }
.fi.active { border-color: var(--sk-primary) !important; background: var(--sk-sel); }
.chip { border: 1px solid var(--sk-chip-line); background: var(--sk-chip-bg); color: var(--sk-chip-text); border-radius: 99px; padding: 2px 9px; font-size: 12px; cursor: pointer; }
#dataText { font-family: ui-monospace, Consolas, monospace; font-size: 12px; min-height: 150px; white-space: pre; }
.dtable { max-height: 260px; overflow: auto; }
.dtable th { position: sticky; top: 0; }
.dtable tbody tr { cursor: pointer; }
.dtable tr.on td { background: var(--sk-sel); }
.ed-center { flex: 1; display: flex; flex-direction: column; min-width: 0; background: var(--sk-canvas); }
#stageWrap { flex: 1; overflow: auto; display: flex; padding: 28px; }
#stage { flex: none; margin: auto; position: relative; box-shadow: 0 6px 30px #0002; background: #fff; user-select: none; touch-action: none; }
#stage img { display: block; width: 100%; height: 100%; pointer-events: none; }
.fld { position: absolute; white-space: pre; line-height: 1.2; cursor: move; padding: 0 2px; outline: 1px dashed transparent; }
.fld:hover { outline-color: var(--sk-primary-soft); }
.fld.on { outline: 2px dashed var(--sk-primary); }
.add-btn { font-size: 12px; padding: 8px 4px; }
.fld-box { padding: 0; }
.fld-box .inner { width: 100%; height: 100%; }
.fld-box img, .fld-box svg { width: 100%; height: 100%; display: block; pointer-events: none; }
.rh { position: absolute; right: -7px; bottom: -7px; width: 14px; height: 14px; background: var(--sk-primary); border: 2px solid #fff; border-radius: 3px; cursor: nwse-resize; box-shadow: 0 0 0 1px var(--sk-primary); }
.vguide { position: absolute; top: 0; bottom: 0; width: 0; border-left: 1px dashed #ef4444; pointer-events: none; display: none; }
@media (max-width: 860px) {
  .ed-body { flex-direction: column-reverse; }
  .ed-left { width: 100%; height: 45%; border-right: 0; border-top: 1px solid var(--sk-line); }
  #pname { max-width: 140px; } .hide-sm { display: none !important; }
  #stageWrap { padding: 12px; }
}
</style>
@endpush

@section('content')
{{-- Bilah editor: nama project, status simpan, ganti desain, generate --}}
<div class="ed-bar d-flex align-items-center gap-2 px-3 border-bottom">
  <a class="btn btn-light border btn-sm" href="{{ url('/dashboard') }}" title="Kembali ke dashboard" aria-label="Kembali"><i class="bi bi-arrow-left"></i></a>
  <input id="pname" class="form-control form-control-sm" aria-label="Nama sertifikat">
  <span id="status" class="small text-muted text-nowrap hide-sm"></span>
  <span class="flex-grow-1"></span>
  <button class="btn btn-light border btn-sm" id="bgBtn"><i class="bi bi-image me-1"></i><span class="hide-sm">Ganti desain</span></button>
  <input type="file" id="bgFile" accept="image/png,image/jpeg,image/webp,image/svg+xml" hidden>
  <button class="btn btn-primary btn-sm" id="genBtn"><i class="bi bi-download me-1"></i>Generate</button>
</div>

<div class="ed-body">
  <div class="ed-left">
    <ul class="nav nav-tabs nav-fill" role="tablist">
      <li class="nav-item"><button class="nav-link active" data-tab="text">Elemen</button></li>
      <li class="nav-item"><button class="nav-link" data-tab="data">Data <span id="rowCount" class="badge text-bg-light border"></span></button></li>
    </ul>

    {{-- TAB ELEMEN --}}
    <div class="ed-pane" id="t-text">
      <div class="small fw-semibold mb-2">Tambah elemen</div>
      <div class="row g-2">
        <div class="col-4"><button class="btn btn-primary w-100 add-btn" id="addBtn"><i class="bi bi-fonts d-block fs-5"></i>Teks</button></div>
        <div class="col-4"><button class="btn btn-outline-primary w-100 add-btn" id="addImgBtn"><i class="bi bi-image d-block fs-5"></i>Gambar</button></div>
        <div class="col-4"><button class="btn btn-outline-primary w-100 add-btn" id="addQrBtn"><i class="bi bi-qr-code d-block fs-5"></i>QR code</button></div>
      </div>
      <input type="file" id="imgFile" accept="image/png,image/jpeg,image/webp,image/svg+xml" hidden>
      <div class="form-text">Gambar: logo, tanda tangan, cap, atau barcode. QR code bisa berbeda tiap penerima.</div>
      <div class="d-grid gap-2 my-3" id="flist"></div>
      <div id="props" class="d-none">
        <div id="boxText">
        <label class="form-label small fw-semibold mb-1" for="pText">Isi teks</label>
        <textarea id="pText" rows="2" class="form-control form-control-sm"></textarea>
        <div class="form-text">Tulis <b>@{{NamaKolom}}</b> untuk diisi dari data. Klik kolom:</div>
        <div class="d-flex flex-wrap gap-1 mt-1" id="pChips"></div>
        <label class="form-label small fw-semibold mb-1 mt-3" for="pFont">Font</label>
        <select id="pFont" class="form-select form-select-sm"></select>
        <div class="row g-2 mt-1">
          <div class="col"><label class="form-label small fw-semibold mb-1" for="pSize">Ukuran (px)</label><input id="pSize" type="number" min="4" max="2000" class="form-control form-control-sm"></div>
          <div class="col"><label class="form-label small fw-semibold mb-1" for="pColor">Warna</label><input id="pColor" type="color" class="form-control form-control-sm form-control-color w-100"></div>
        </div>
        <div class="row g-2 mt-1">
          <div class="col"><div class="small fw-semibold mb-1">Rata</div>
            <div class="btn-group btn-group-sm w-100" id="pAlign">
              <button class="btn btn-outline-secondary" data-v="left" title="Kiri"><i class="bi bi-text-left"></i></button>
              <button class="btn btn-outline-secondary" data-v="center" title="Tengah"><i class="bi bi-text-center"></i></button>
              <button class="btn btn-outline-secondary" data-v="right" title="Kanan"><i class="bi bi-text-right"></i></button>
            </div></div>
          <div class="col"><div class="small fw-semibold mb-1">Gaya</div>
            <div class="btn-group btn-group-sm w-100">
              <button class="btn btn-outline-secondary" id="pBold" title="Tebal"><i class="bi bi-type-bold"></i></button>
              <button class="btn btn-outline-secondary" id="pItalic" title="Miring"><i class="bi bi-type-italic"></i></button>
            </div></div>
        </div>
        <label class="form-label small fw-semibold mb-1 mt-2" for="pMax">Lebar maksimal (0 = bebas)</label>
        <input id="pMax" type="number" min="0" class="form-control form-control-sm">
        <div class="form-text">Teks yang lebih panjang otomatis mengecil. Berguna untuk nama panjang.</div>
        </div>

        {{-- pengaturan gambar --}}
        <div id="boxImg">
          <div class="d-flex align-items-center gap-2">
            <img id="pImgPrev" alt="Pratinjau gambar" class="border rounded bg-light" style="width:64px;height:64px;object-fit:contain">
            <button class="btn btn-outline-secondary btn-sm" id="pImgChange"><i class="bi bi-arrow-repeat me-1"></i>Ganti gambar</button>
          </div>
          <label class="form-label small fw-semibold mb-1 mt-3" for="pImgW">Lebar (px)</label>
          <input id="pImgW" type="number" min="10" class="form-control form-control-sm">
          <label class="form-label small fw-semibold mb-1 mt-2" for="pOpacity">Transparansi: <span id="pOpacityVal"></span></label>
          <input id="pOpacity" type="range" min="10" max="100" class="form-range">
          <div class="form-text">Tarik kotak biru di pojok gambar untuk mengubah ukuran.</div>
        </div>

        {{-- pengaturan QR --}}
        <div id="boxQr">
          <label class="form-label small fw-semibold mb-1" for="pQrText">Isi QR code</label>
          <textarea id="pQrText" rows="2" class="form-control form-control-sm" placeholder="https://contoh.com/verifikasi?nama=@{{Nama}}"></textarea>
          <div class="form-text">Bisa link atau teks. Pakai <b>@{{NamaKolom}}</b> supaya QR tiap orang berbeda. Klik kolom:</div>
          <div class="d-flex flex-wrap gap-1 mt-1" id="pQrChips"></div>
          <div class="row g-2 mt-2">
            <div class="col"><label class="form-label small fw-semibold mb-1" for="pQrSize">Ukuran (px)</label><input id="pQrSize" type="number" min="40" class="form-control form-control-sm"></div>
            <div class="col"><label class="form-label small fw-semibold mb-1" for="pQrColor">Warna</label><input id="pQrColor" type="color" class="form-control form-control-sm form-control-color w-100"></div>
          </div>
          <div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="pQrBg"><label class="form-check-label small" for="pQrBg">Latar putih (lebih mudah di-scan)</label></div>
        </div>

        {{-- pengaturan umum --}}
        <div class="row g-2 mt-1">
          <div class="col"><label class="form-label small fw-semibold mb-1" for="pX">Posisi X</label><input id="pX" type="number" class="form-control form-control-sm"></div>
          <div class="col"><label class="form-label small fw-semibold mb-1" for="pY">Posisi Y</label><input id="pY" type="number" class="form-control form-control-sm"></div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <button class="btn btn-outline-secondary btn-sm" id="centerBtn"><i class="bi bi-arrows-collapse-vertical me-1"></i>Ke tengah</button>
          <button class="btn btn-outline-secondary btn-sm" id="upBtn" title="Pindah ke lapisan depan"><i class="bi bi-layer-forward"></i></button>
          <button class="btn btn-outline-secondary btn-sm" id="downBtn" title="Pindah ke lapisan belakang"><i class="bi bi-layer-backward"></i></button>
          <button class="btn btn-outline-secondary btn-sm" id="dupBtn"><i class="bi bi-copy me-1"></i>Duplikat</button>
          <button class="btn btn-outline-danger btn-sm" id="delBtn"><i class="bi bi-trash me-1"></i>Hapus</button>
        </div>
      </div>
    </div>

    {{-- TAB DATA --}}
    <div class="ed-pane d-none" id="t-data">
      <p class="form-text mt-0">Baris pertama = nama kolom. Tempel dari Excel/Google Sheets atau upload CSV. Satu baris = satu sertifikat.</p>
      <div class="d-flex gap-2 mb-2">
        <button class="btn btn-outline-secondary btn-sm" id="csvBtn"><i class="bi bi-filetype-csv me-1"></i>Upload CSV</button>
        <input type="file" id="csvFile" accept=".csv,.txt,.tsv" hidden>
        <button class="btn btn-outline-secondary btn-sm" id="clearBtn">Kosongkan</button>
      </div>
      <textarea id="dataText" class="form-control" spellcheck="false" aria-label="Data penerima" placeholder="Nama,Asal&#10;Budi Santoso,SMA 1&#10;Siti Aminah,SMA 2"></textarea>
      <div class="d-flex flex-wrap gap-1 mt-2" id="colChips"></div>
      <div class="dtable border rounded mt-2" id="dtable"></div>
    </div>
  </div>

  <div class="ed-center">
    <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom small" style="background:var(--sk-panel)">
      Pratinjau baris
      <button class="btn btn-light border btn-sm py-0" id="prevRow" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></button>
      <b id="rowLabel">-</b>
      <button class="btn btn-light border btn-sm py-0" id="nextRow" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></button>
      <span class="flex-grow-1"></span><span id="sizeLabel" class="text-muted hide-sm me-2"></span>
      <div class="btn-group btn-group-sm" role="group" aria-label="Zoom">
        <button class="btn btn-light border py-0" id="zoomOut" title="Perkecil (Ctrl -)" aria-label="Perkecil"><i class="bi bi-zoom-out"></i></button>
        <button class="btn btn-light border py-0 fw-semibold" id="zoomLabel" title="Klik untuk 100%" style="min-width:54px">100%</button>
        <button class="btn btn-light border py-0" id="zoomIn" title="Perbesar (Ctrl +)" aria-label="Perbesar"><i class="bi bi-zoom-in"></i></button>
      </div>
      <button class="btn btn-light border btn-sm py-0" id="zoomFit" title="Pas layar (Ctrl 0)"><i class="bi bi-arrows-angle-contract me-1"></i><span class="hide-sm">Pas layar</span></button>
    </div>
    <div id="stageWrap"><div id="stage"><img id="bg" alt=""><div class="vguide" id="vguide"></div></div></div>
  </div>
</div>

{{-- Modal Generate --}}
<div class="modal fade" id="genModal" tabindex="-1" aria-labelledby="genTitle" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h2 class="modal-title fs-5" id="genTitle">Generate sertifikat</h2><button class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" id="genClose"></button></div>
    <div class="modal-body">
      <label class="form-label small fw-semibold" for="gFormat">Format</label>
      <select id="gFormat" class="form-select mb-3">
        <option value="pdf">PDF, semua dalam satu file</option>
        <option value="pdfzip">PDF, satu file per orang (ZIP)</option>
        <option value="pngzip">PNG, satu gambar per orang (ZIP)</option>
        <option value="jpgzip">JPG, satu gambar per orang (ZIP)</option>
      </select>
      <label class="form-label small fw-semibold" for="gName">Nama file</label>
      <input id="gName" class="form-control">
      <div class="form-text">Bisa pakai @{{NamaKolom}} dan @{{no}} (nomor urut).</div>
      <div class="border rounded p-3 mt-3 small" id="gSum" style="background:var(--sk-panel)"></div>
      <div class="progress mt-3 d-none" id="gProg" role="progressbar" aria-label="Progres generate"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width:0"></div></div>
      <div class="small text-muted mt-2" id="gMsg" role="status"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-primary w-100" id="gGo"><i class="bi bi-download me-1"></i>Generate &amp; Download</button></div>
  </div></div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
{{--
  Untuk tim Backend: kirim variabel ini dari controller, contoh:
    return view('sertifikat.edit', [
        'project' => [...],   // { name, width, height, image, fields, data }
        'saveUrl' => route('sertifikat.update', $id),   // menerima PUT JSON dengan isi yang sama
    ]);
  Selama masih kosong, editor jalan dalam mode draf (tersimpan di browser).
--}}
<script>
window.MYSERTI_EDITOR = {
  project: @json($project ?? null),
  saveUrl: @json($saveUrl ?? null),
  historyUrl: @json($historyUrl ?? null),
  fontsUrl: null,
  csrf: @json(csrf_token()),
};
</script>
<script src="{{ asset('js/common.js') }}"></script>
<script src="{{ asset('js/editor-laravel.js') }}"></script>
@endpush
