@extends('layouts.app')
@section('page-title', 'Home')
@section('breadcrumb', '')
@section('landing', '1') {{-- tanpa judul halaman & pembungkus: landing memakai lebar penuh --}}

@push('styles')
<style>
html { scroll-behavior: smooth; scroll-padding-top: 76px; }

/* jarak antarbagian sama di semua bagian */
.lp-section { padding-top: 96px; padding-bottom: 96px; }
.lp-section + .lp-section { border-top: 1px solid var(--sk-line); }
.lp-section h2 { font-weight: 800; font-size: clamp(28px, 3.2vw, 40px); letter-spacing: -.8px; line-height: 1.15; }
.lp-lead { font-size: 18px; color: var(--sk-muted); }
@media (max-width: 767px) { .lp-section { padding-top: 64px; padding-bottom: 64px; } }

/* hero */
.lp-hero { padding-top: 72px; padding-bottom: 96px; }
.lp-hero h1 { font-size: clamp(36px, 5vw, 60px); font-weight: 800; line-height: 1.05; letter-spacing: -1.8px; margin: 0 0 20px; }
.lp-hero .lp-lead { max-width: 500px; }
.lp-facts { display: flex; flex-wrap: wrap; gap: 8px 20px; margin-top: 28px; color: var(--sk-muted); font-size: 14px; }
.lp-facts span { display: inline-flex; align-items: center; gap: 6px; }
.lp-facts i { color: var(--sk-primary); }

/* jendela aplikasi (dipakai di hero) */
.lp-win { background: var(--sk-surface); border: 1px solid var(--sk-line); border-radius: 14px; box-shadow: var(--sk-shadow); overflow: hidden; }
.lp-win__bar { display: flex; align-items: center; gap: 8px; height: 42px; padding: 0 14px; border-bottom: 1px solid var(--sk-line); background: var(--sk-panel); font-size: 13px; }
.lp-win__bar i.dot { width: 9px; height: 9px; border-radius: 50%; background: var(--sk-line); }
.lp-win__bar b { margin-left: 6px; font-weight: 700; }
.lp-win__go { margin-left: auto; background: var(--sk-primary); color: #fff; border-radius: 99px; padding: 3px 12px; font-weight: 600; }
[data-bs-theme="dark"] .lp-win__go { color: #062320; }
.lp-win__body { display: grid; grid-template-columns: 190px 1fr; }
.lp-names { border-right: 1px solid var(--sk-line); padding: 8px; font-size: 13px; }
.lp-names__h { display: flex; justify-content: space-between; padding: 6px 8px; color: var(--sk-muted); font-size: 12px; }
.lp-names__r { display: flex; justify-content: space-between; gap: 8px; padding: 7px 8px; border-radius: 8px; transition: background .25s, color .25s; }
.lp-names__r em { font-style: normal; color: var(--sk-muted); }
.lp-names__r.on { background: var(--sk-primary-tint); color: var(--sk-primary); font-weight: 700; }
.lp-names__r.done span::after { content: " \2713"; color: var(--sk-primary); }
.lp-stage { padding: 18px; background: var(--sk-canvas); }
.lp-cert { border-radius: 6px; overflow: hidden; box-shadow: 0 8px 24px rgb(0 0 0 / .12); background: #fff; }
.lp-cert svg { display: block; width: 100%; height: auto; }
#certName { transition: opacity .2s; }
.lp-win__foot { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-top: 1px solid var(--sk-line); font-size: 13px; color: var(--sk-muted); }
.lp-win__foot .progress { flex: 1; height: 6px; }
.lp-win__foot b { color: var(--sk-text); font-variant-numeric: tabular-nums; }
@media (max-width: 575px) { .lp-win__body { grid-template-columns: 1fr; } .lp-names { display: none; } }

/* kartu langkah: gambar kecil di bawah teks */
.lp-step { height: 100%; }
.lp-step__n { width: 28px; height: 28px; border-radius: 50%; border: 1px solid var(--sk-line); display: inline-grid; place-items: center; font-size: 13px; font-weight: 700; margin-bottom: 14px; }
.lp-step h3 { font-size: 18px; font-weight: 700; margin-bottom: 6px; }
.lp-step p { color: var(--sk-muted); margin-bottom: 20px; }
.lp-mini { background: var(--sk-panel); border: 1px solid var(--sk-line); border-radius: 12px; padding: 16px; height: 220px; overflow: hidden; }
.lp-mini__card { background: var(--sk-surface); border: 1px solid var(--sk-line); border-radius: 10px; overflow: hidden; font-size: 13px; }
.lp-mini__h { display: flex; justify-content: space-between; padding: 9px 12px; border-bottom: 1px solid var(--sk-line); }
.lp-mini__h span { color: var(--sk-muted); }
.lp-mini table { width: 100%; font-size: 12.5px; }
.lp-mini td, .lp-mini th { padding: 7px 12px; border-bottom: 1px solid var(--sk-line); }
.lp-mini th { color: var(--sk-muted); font-weight: 600; }
.lp-mini ul { list-style: none; margin: 0; padding: 4px 0; }
.lp-mini li { display: flex; align-items: center; gap: 8px; padding: 7px 12px; }
.lp-mini li i { color: #dc2626; }
.lp-mini li span { margin-left: auto; color: var(--sk-muted); font-size: 12px; }
.lp-mini__thumb { padding: 12px; }
.lp-mini__thumb svg { display: block; width: 100%; height: auto; border-radius: 4px; }

/* kalimat besar */
.lp-statement { font-size: clamp(24px, 2.8vw, 34px); font-weight: 700; line-height: 1.35; letter-spacing: -.5px; max-width: 900px; }
.lp-statement span { color: var(--sk-muted); }

/* fitur */
.lp-feat { display: flex; gap: 14px; padding: 18px 0; border-top: 1px solid var(--sk-line); }
.lp-feat:first-child { border-top: 0; padding-top: 0; }
.lp-feat i { font-size: 20px; color: var(--sk-primary); line-height: 1.3; }
.lp-feat h3 { font-size: 16px; font-weight: 700; margin: 0 0 4px; }
.lp-feat p { margin: 0; color: var(--sk-muted); }
.lp-qr { background: var(--sk-panel); border: 1px solid var(--sk-line); border-radius: 14px; padding: 28px; }

/* pertanyaan: jawaban langsung terbuka */
.lp-faq h3 { font-size: 17px; font-weight: 700; margin-bottom: 6px; }
.lp-faq p { color: var(--sk-muted); margin: 0; }
.lp-faq > div { padding: 20px 0; border-top: 1px solid var(--sk-line); }
.lp-formats { display: grid; gap: 10px; margin-top: 24px; }
.lp-formats > div { display: flex; align-items: center; gap: 12px; }
.lp-formats i { font-size: 24px; }
.lp-formats small { display: block; color: var(--sk-muted); }

.lp-footer a { color: var(--sk-muted); text-decoration: none; }
.lp-footer a:hover { color: var(--sk-primary); }
@media (prefers-reduced-motion: reduce) { * { transition: none !important; scroll-behavior: auto !important; } }
</style>
@endpush

@section('content')
{{-- Hero: teks kiri, jendela editor kanan (menggantikan "Welcome to MySerti") --}}
<header class="container lp-hero">
  <div class="row align-items-center g-5">
    <div class="col-lg-5">
      <h1>Sertifikat massal untuk panitia acara</h1>
      <p class="lp-lead">Upload desain sertifikatmu, tempel daftar peserta dari Excel, lalu MySerti membuat satu sertifikat untuk setiap nama dan mengunduh semuanya sekaligus.</p>
      <div class="d-flex flex-wrap gap-2 mt-4">
        @auth
          <a class="btn btn-primary btn-lg px-4" href="{{ url('/dashboard') }}">Buka dashboard</a>
        @else
          <a class="btn btn-primary btn-lg px-4" href="{{ route('register') }}">Buat akun</a>
        @endauth
        <a class="btn btn-light border btn-lg px-4" href="#cara-kerja">Lihat cara kerja</a>
      </div>
      <div class="lp-facts">
        <span><i class="bi bi-browser-chrome"></i>Jalan di browser</span>
        <span><i class="bi bi-filetype-pdf"></i>PDF, PNG, JPG, ZIP</span>
        <span><i class="bi bi-qr-code"></i>QR per peserta</span>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="lp-win" role="img" aria-label="Editor MySerti membuat sertifikat Seminar Nasional 2026 dari daftar nama peserta">
        <div class="lp-win__bar" aria-hidden="true"><i class="dot"></i><i class="dot"></i><i class="dot"></i><b>Seminar Nasional 2026</b><span class="lp-win__go">Generate</span></div>
        <div class="lp-win__body" aria-hidden="true">
          <div class="lp-names">
            <div class="lp-names__h"><span>Nama</span><span>Instansi</span></div>
            <div id="sheet"></div>
          </div>
          <div class="lp-stage">
            <div class="lp-cert">
              <svg viewBox="0 0 800 566">
                <rect width="800" height="566" fill="#fdfcf7"/>
                <rect x="22" y="22" width="756" height="522" fill="none" stroke="#0f766e" stroke-width="8"/>
                <rect x="40" y="40" width="720" height="486" fill="none" stroke="#f59e0b" stroke-width="2"/>
                <text x="400" y="140" text-anchor="middle" font-family="Georgia,serif" font-size="46" font-weight="bold" fill="#0f766e" letter-spacing="8">SERTIFIKAT</text>
                <text x="400" y="190" text-anchor="middle" font-family="Georgia,serif" font-size="18" fill="#555">diberikan kepada</text>
                <text id="certName" x="400" y="282" text-anchor="middle" font-family="'Great Vibes',cursive" font-size="64" fill="#1f2937">Budi Santoso</text>
                <line x1="210" y1="306" x2="590" y2="306" stroke="#f59e0b" stroke-width="2"/>
                <text x="400" y="346" text-anchor="middle" font-family="Georgia,serif" font-size="17" fill="#555">atas partisipasinya dalam Seminar Nasional 2026</text>
                <g id="certQr" transform="translate(76 404)"></g>
                <line x1="480" y1="460" x2="680" y2="460" stroke="#555"/>
                <text x="580" y="484" text-anchor="middle" font-family="Georgia,serif" font-size="14" fill="#555">Ketua Panitia</text>
              </svg>
            </div>
          </div>
        </div>
        <div class="lp-win__foot" aria-hidden="true"><span>Dibuat</span><b id="counter">1 / 6</b><div class="progress" role="presentation"><div class="progress-bar" id="bar" style="width:16%"></div></div><span id="fileName">Budi Santoso.pdf</span></div>
      </div>
    </div>
  </div>
</header>

{{-- Tiga langkah --}}
<section class="container lp-section" id="cara-kerja">
  <h2 class="mb-2">Sertifikat jadi dalam 3 langkah</h2>
  <p class="lp-lead mb-5">Desain dari aplikasi apa pun, data dari spreadsheet yang sudah kamu punya.</p>
  <div class="row g-4">
    <div class="col-md-4"><div class="lp-step">
      <span class="lp-step__n">1</span>
      <h3>Upload desain sertifikat</h3>
      <p>Pakai desain dari Canva atau Photoshop dalam JPG, PNG, WEBP atau SVG, atau pilih template.</p>
      <div class="lp-mini" aria-hidden="true"><div class="lp-mini__card">
        <div class="lp-mini__h"><b>sertifikat-seminar.png</b><span>2000 × 1414</span></div>
        <div class="lp-mini__thumb"><svg viewBox="0 0 800 566"><rect width="800" height="566" fill="#fdfcf7"/><rect x="22" y="22" width="756" height="522" fill="none" stroke="#0f766e" stroke-width="8"/><rect x="40" y="40" width="720" height="486" fill="none" stroke="#f59e0b" stroke-width="2"/><text x="400" y="150" text-anchor="middle" font-family="Georgia,serif" font-size="46" font-weight="bold" fill="#0f766e" letter-spacing="8">SERTIFIKAT</text><line x1="210" y1="306" x2="590" y2="306" stroke="#f59e0b" stroke-width="2" stroke-dasharray="10 8"/></svg></div>
      </div></div>
    </div></div>
    <div class="col-md-4"><div class="lp-step">
      <span class="lp-step__n">2</span>
      <h3>Tempel daftar nama</h3>
      <p>Salin kolom dari Excel atau Google Sheets. Baris pertama menjadi nama kolom.</p>
      <div class="lp-mini" aria-hidden="true"><div class="lp-mini__card">
        <div class="lp-mini__h"><b>Data</b><span>120 baris</span></div>
        <table><tr><th>Nama</th><th>Instansi</th></tr><tr><td>Budi Santoso</td><td>UNS</td></tr><tr><td>Siti Aminah</td><td>UGM</td></tr><tr><td>Andi Wijaya</td><td>ITS</td></tr><tr><td>Dewi Lestari</td><td>UNY</td></tr></table>
      </div></div>
    </div></div>
    <div class="col-md-4"><div class="lp-step">
      <span class="lp-step__n">3</span>
      <h3>Unduh semua sertifikat</h3>
      <p>Satu PDF untuk dicetak, atau ZIP berisi satu file per peserta dengan namanya.</p>
      <div class="lp-mini" aria-hidden="true"><div class="lp-mini__card">
        <div class="lp-mini__h"><b>Seminar Nasional 2026.zip</b><span>120 file</span></div>
        <ul><li><i class="bi bi-filetype-pdf"></i>Budi Santoso.pdf<span>412 KB</span></li><li><i class="bi bi-filetype-pdf"></i>Siti Aminah.pdf<span>409 KB</span></li><li><i class="bi bi-filetype-pdf"></i>Andi Wijaya.pdf<span>411 KB</span></li><li><i class="bi bi-filetype-pdf"></i>Dewi Lestari.pdf<span>410 KB</span></li></ul>
      </div></div>
    </div></div>
  </div>
</section>

{{-- Kenapa dibuat --}}
<section class="container lp-section">
  <p class="lp-statement mb-0">Panitia biasanya mengetik ulang nama peserta satu per satu di Word atau Canva, lalu menyimpan file satu per satu. <span>Di MySerti daftar nama ditempel sekali, dan setiap baris menjadi satu sertifikat dengan nama file pesertanya.</span></p>
</section>

{{-- Fitur --}}
<section class="container lp-section" id="fitur">
  <div class="row g-5 align-items-start">
    <div class="col-lg-5">
      <h2 class="mb-3">Atur sekali di editor, berlaku untuk semua peserta</h2>
      <p class="lp-lead mb-4">Semua yang kamu ubah di satu sertifikat langsung dipakai untuk seluruh daftar.</p>
      <div>
        <div class="lp-feat"><i class="bi bi-arrows-move"></i><div><h3>Geser teks di atas desain</h3><p>Atur posisi, font, ukuran dan warna, lengkap dengan zoom.</p></div></div>
        <div class="lp-feat"><i class="bi bi-arrows-angle-contract"></i><div><h3>Nama panjang tetap muat</h3><p>Isi lebar maksimal, dan nama yang terlalu panjang mengecil sendiri.</p></div></div>
        <div class="lp-feat"><i class="bi bi-image"></i><div><h3>Logo, tanda tangan dan cap</h3><p>Tambahkan gambar dan atur transparansinya.</p></div></div>
        <div class="lp-feat"><i class="bi bi-building"></i><div><h3>Kerja bareng organisasi</h3><p>Buat organisasi, bagikan kodenya, dan kelola sertifikat bersama anggota panitia.</p></div></div>
      </div>
    </div>
    <div class="col-lg-6 offset-lg-1">
      <div class="lp-qr">
        <h3 class="h5 fw-bold mb-2">QR code yang berbeda di setiap sertifikat</h3>
        <p class="text-muted mb-4">Isi QR boleh memakai kolom dari daftar, misalnya nama atau nomor urut. Pemeriksa cukup memindai untuk tahu sertifikat itu milik siapa.</p>
        <div class="lp-cert">
          <svg viewBox="0 0 800 566" role="img" aria-label="Sertifikat Andi Wijaya dengan QR code berisi namanya">
            <rect width="800" height="566" fill="#fdfcf7"/>
            <rect x="22" y="22" width="756" height="522" fill="none" stroke="#0f766e" stroke-width="8"/>
            <rect x="40" y="40" width="720" height="486" fill="none" stroke="#f59e0b" stroke-width="2"/>
            <text x="400" y="140" text-anchor="middle" font-family="Georgia,serif" font-size="46" font-weight="bold" fill="#0f766e" letter-spacing="8">SERTIFIKAT</text>
            <text x="400" y="190" text-anchor="middle" font-family="Georgia,serif" font-size="18" fill="#555">diberikan kepada</text>
            <text x="400" y="282" text-anchor="middle" font-family="'Great Vibes',cursive" font-size="64" fill="#1f2937">Andi Wijaya</text>
            <line x1="210" y1="306" x2="590" y2="306" stroke="#f59e0b" stroke-width="2"/>
            <text x="400" y="346" text-anchor="middle" font-family="Georgia,serif" font-size="17" fill="#555">atas partisipasinya dalam Seminar Nasional 2026</text>
            <g id="qrStatic" transform="translate(76 404)"></g>
            <line x1="480" y1="460" x2="680" y2="460" stroke="#555"/>
            <text x="580" y="484" text-anchor="middle" font-family="Georgia,serif" font-size="14" fill="#555">Ketua Panitia</text>
          </svg>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Pertanyaan --}}
<section class="container lp-section" id="faq">
  <div class="row g-5">
    <div class="col-lg-5">
      <h2 class="mb-3">Pertanyaan yang sering muncul</h2>
      <p class="lp-lead">Data yang bisa dipakai dan file yang bisa diunduh:</p>
      <div class="lp-formats">
        <div><i class="bi bi-file-earmark-spreadsheet text-success"></i><div>Excel, Google Sheets, CSV<small>Salin kolom lalu tempel, atau upload CSV</small></div></div>
        <div><i class="bi bi-file-earmark-pdf text-danger"></i><div>PDF<small>Satu file untuk dicetak, atau satu per peserta</small></div></div>
        <div><i class="bi bi-file-earmark-zip text-warning"></i><div>ZIP<small>PDF, PNG atau JPG per peserta</small></div></div>
      </div>
    </div>
    <div class="col-lg-6 offset-lg-1 lp-faq">
      <div><h3>Format data apa yang bisa dipakai?</h3><p>Tempel langsung dari Excel atau Google Sheets, atau upload CSV dengan pemisah koma, titik koma atau tab.</p></div>
      <div><h3>Bisa memakai desain sendiri?</h3><p>Bisa. Upload desain dalam JPG, PNG, WEBP atau SVG, lalu letakkan teks dan QR di atasnya.</p></div>
      <div><h3>Isi QR code bisa berbeda tiap orang?</h3><p>Bisa. Masukkan kolom nama atau nomor urut ke isi QR, dan setiap sertifikat mendapat QR sendiri.</p></div>
      <div><h3>Apa gunanya organisasi?</h3><p>Satu organisasi, misalnya himpunan atau panitia acara, bisa punya banyak anggota. Owner membagikan kode organisasi, dan anggota bergabung memakai kode itu.</p></div>
      <div><h3>Apakah sertifikat dikirim lewat email?</h3><p>Belum. MySerti membuat dan mengunduh file sertifikat; membagikannya ke peserta masih kamu lakukan sendiri.</p></div>
    </div>
  </div>
</section>

{{-- Ajakan: hanya untuk tamu --}}
@guest
<section class="container lp-section">
  <div class="d-lg-flex align-items-end justify-content-between gap-4">
    <div>
      <h2 class="mb-2">Buat sertifikat acaramu berikutnya</h2>
      <p class="lp-lead mb-0">Daftar dengan email, lalu upload desain pertamamu.</p>
    </div>
    <a class="btn btn-primary btn-lg px-4 mt-4 mt-lg-0 flex-none" href="{{ route('register') }}">Buat akun</a>
  </div>
</section>
@endguest

<footer class="border-top py-4 lp-footer">
  <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3 small text-muted">
    <span class="d-flex align-items-center gap-2"><span class="logo" style="font-size:15px"><img src="{{ asset('img/logo.webp') }}" alt="" style="width:28px"><b>My<span>Serti</span></b></span> &copy; {{ date('Y') }}</span>
    <span class="d-flex gap-3"><a href="#cara-kerja">Cara kerja</a><a href="#fitur">Fitur</a><a href="#faq">Pertanyaan</a>@guest<a href="{{ route('login') }}">Masuk</a>@endguest</span>
  </div>
</footer>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
<script>
const $id = id => document.getElementById(id);

// QR code asli (bisa dipindai) dalam bentuk SVG
function qrPath(text) {
  if (!window.qrcode) return '';
  const q = qrcode(0, 'M'); q.addData(text); q.make();
  const n = q.getModuleCount(), k = 90 / n; let d = '';
  for (let r = 0; r < n; r++) for (let c = 0; c < n; c++) if (q.isDark(r, c)) d += `M${(c * k).toFixed(2)},${(r * k).toFixed(2)}h${k.toFixed(2)}v${k.toFixed(2)}h-${k.toFixed(2)}z`;
  return `<rect x="-5" y="-5" width="100" height="100" fill="#fff"/><path d="${d}" fill="#111"/>`;
}
$id('qrStatic').innerHTML = qrPath('MySerti: Andi Wijaya');

// animasi hero: nama di daftar berganti satu per satu menjadi sertifikat
const people = [['Budi Santoso', 'UNS'], ['Siti Aminah', 'UGM'], ['Andi Wijaya', 'ITS'], ['Dewi Lestari', 'UNY'], ['Rizky Pratama', 'UNDIP'], ['Nur Aisyah', 'UNAIR']];
$id('sheet').innerHTML = people.map(([n, s]) => `<div class="lp-names__r"><span>${n}</span><em>${s}</em></div>`).join('');
const rows = [...document.querySelectorAll('#sheet .lp-names__r')];
let i = 0;
function step() {
  rows.forEach((r, j) => { r.classList.toggle('on', j === i); r.classList.toggle('done', j < i); });
  const el = $id('certName'), name = people[i][0]; el.style.opacity = 0;
  setTimeout(() => { el.textContent = name; $id('certQr').innerHTML = qrPath('MySerti: ' + name); el.style.opacity = 1; }, 180);
  $id('counter').textContent = `${i + 1} / ${people.length}`;
  $id('fileName').textContent = name + '.pdf';
  $id('bar').style.width = Math.round((i + 1) / people.length * 100) + '%';
  i = (i + 1) % people.length;
}
step();
if (!matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(step, 1500);
</script>
@endpush
