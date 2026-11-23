// Aplikasi utama: My Projects, Custom Fonts, Riwayat & Laporan
// Pindah halaman memakai hash (#/files, #/fonts, #/history) supaya jalan tanpa server.
const $ = id => document.getElementById(id);
const view = $('view');
let me = null;

// util
function modal(title, bodyHtml, footHtml = '<button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>') {
  $('infoTitle').textContent = title; $('infoBody').innerHTML = bodyHtml; $('infoFoot').innerHTML = footHtml;
  const m = bootstrap.Modal.getOrCreateInstance($('infoModal')); m.show(); return m;
}
function confirmBox(title, text, okLabel = 'Ya', danger = true) {
  return new Promise(ok => {
    const m = modal(title, `<p class="mb-0">${esc(text)}</p>`,
      `<button class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-${danger ? 'danger' : 'primary'}" id="cfOk">${esc(okLabel)}</button>`);
    let done = false;
    $('cfOk').onclick = () => { done = true; m.hide(); ok(true); };
    $('infoModal').addEventListener('hidden.bs.modal', () => { if (!done) ok(false); }, { once: true });
  });
}
const handleErr = e => toast(e.message, 1);

// Alamat halaman dari backend (mis. /editor/abc, /files) diubah ke alamat frontend statis
function staticLink(l) {
  if (!l) return '#/files';
  if (l.startsWith('/editor/')) return 'editor.html?id=' + encodeURIComponent(l.slice(8));
  if (['/files', '/fonts', '/history'].includes(l)) return '#' + l;
  return '#/files';
}

// akun & menu atas
async function refreshMe() {
  me = await api('/api/auth/me');
  $('uname').textContent = me.name; $('uemail').textContent = me.email;
  $('av').textContent = (me.name[0] || '?').toUpperCase();
  return me;
}

async function renderNotifs() {
  const { list, unread } = await api('/api/notifications');
  $('notifCount').textContent = unread > 9 ? '9+' : unread;
  $('notifCount').classList.toggle('d-none', !unread);
  $('notifMenu').innerHTML = `<div class="px-3 py-2 border-bottom fw-semibold small">Notifikasi</div>` + (list.length
    ? list.map(n => `<a class="dropdown-item py-2 border-bottom text-wrap ${n.is_read ? '' : 'unread'}" href="${esc(staticLink(n.link))}">
        <div class="fw-semibold small">${esc(n.title)}</div><div class="small text-muted">${esc(n.body)}</div>
        <div class="text-muted" style="font-size:11px">${fmtDate(n.created_at, 1)}</div></a>`).join('')
    : '<div class="p-3 small text-muted">Belum ada notifikasi.</div>');
}
$('notifBtn').addEventListener('shown.bs.dropdown', async () => {
  if ($('notifCount').classList.contains('d-none')) return;
  await api('/api/notifications/read', { body: {} });
  $('notifCount').classList.add('d-none');
});

$('logout').onclick = async e => {
  e.preventDefault();
  try { await api('/api/auth/logout', { body: {} }); } catch {}
  location.href = 'login.html';
};
$('tutBtn').onclick = e => {
  e.preventDefault();
  modal('Tutorial singkat', `<ol class="ps-3 mb-0 lh-lg"><li>Upload desain sertifikat, atau pilih <b>Templates</b>.</li>
    <li>Di editor, tambah teks dan tulis <code>{{Nama}}</code> sebagai tempat isian.</li>
    <li>Buka tab <b>Data</b>, tempel daftar nama (baris pertama = judul kolom) atau upload CSV.</li>
    <li>Klik <b>Generate</b>, pilih PDF/PNG/JPG, lalu download.</li></ol>`);
};

// router (hash)
const routes = {
  '/files': [viewProjects, 'Project'],
  '/fonts': [viewFonts, 'Font'],
  '/history': [viewHistory, 'Riwayat'],
};
async function render() {
  const path = routes[location.hash.slice(1)] ? location.hash.slice(1) : '/files';
  const [fn, title] = routes[path];
  document.title = title + ' | MySerti';
  document.querySelectorAll('.sk-topbar .nav-link').forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + path));
  bootstrap.Collapse.getInstance($('topnav'))?.hide(); // tutup menu di HP setelah pindah halaman
  view.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm me-2"></div>Memuat...</div>';
  try { await fn(); }
  catch (e) { view.innerHTML = `<div class="alert alert-danger">${esc(e.message)}</div>`; }
  window.scrollTo(0, 0);
}
window.addEventListener('hashchange', render);

// My Projects
async function viewProjects() {
  const list = await api('/api/projects');
  const first = (me?.name || '').split(' ')[0];
  view.innerHTML = `
  <h1 class="h3 sk-hello mb-1">${first ? `Halo, ${esc(first)}!` : 'Halo!'} Mau buat sertifikat apa hari ini?</h1>
  <p class="text-muted mb-4">Pilih desain, isi daftar nama, lalu unduh semuanya sekaligus.</p>
  <section class="sk-start">
    <div class="sk-option sk-drop" id="drop" role="button" tabindex="0" aria-label="Upload desain sertifikat">
      <div class="ico"><i class="bi bi-cloud-arrow-up"></i></div>
      <div>
        <h3 id="dropText">Upload desain sertifikat</h3>
        <p>Tarik file ke sini atau klik untuk memilih (JPG, PNG, WEBP, SVG)</p>
        <div class="sk-steps"><span>1 Desain</span><span>2 Data nama</span><span>3 Unduh</span></div>
      </div>
    </div>
    <button class="sk-option" id="tplBtn"><div class="ico"><i class="bi bi-layout-text-window"></i></div>
      <div><h3>Pakai template</h3><p>6 desain siap pakai, tinggal isi nama.</p></div></button>
    <button class="sk-option" id="blankBtn"><div class="ico"><i class="bi bi-file-earmark-plus"></i></div>
      <div><h3>Kanvas kosong</h3><p>Mulai dari halaman putih ukuran A4 mendatar.</p></div></button>
    <input type="file" id="file" accept=".jpg,.jpeg,.png,.webp,.svg" hidden>
  </section>
  <div class="d-flex align-items-center gap-2 mt-5 mb-3">
    <h2 class="h5 fw-bold m-0">Project kamu</h2>
    <span class="badge rounded-pill text-bg-light border">${list.length}</span>
  </div>
  <div class="row g-3" id="list"></div>`;

  const el = $('list');
  if (!list.length) el.innerHTML = '<div class="col-12"><div class="sk-empty"><i class="bi bi-inboxes fs-2 d-block mb-2"></i>Belum ada project. Mulai dengan upload desain atau pilih template di atas.</div></div>';
  for (const p of list) {
    const col = document.createElement('div'); col.className = 'col-sm-6 col-lg-4 col-xl-3';
    col.innerHTML = `<a class="sk-proj" href="editor.html?id=${encodeURIComponent(p.id)}">
      <div class="thumb" style="background-image:url('${p.image}')"></div>
      <div class="p-2 px-3 border-top small"><div class="fw-semibold text-truncate">${esc(p.name)}</div><div class="text-muted">Diubah ${fmtDate(p.updated)}</div></div>
      <button class="btn btn-dark btn-sm rounded-circle del" title="Hapus" aria-label="Hapus project"><i class="bi bi-trash"></i></button></a>`;
    col.querySelector('.del').onclick = async e => {
      e.preventDefault(); e.stopPropagation();
      if (!(await confirmBox('Hapus project?', `Project "${p.name}" akan dihapus permanen dan tidak bisa dikembalikan.`, 'Hapus'))) return;
      try { await api('/api/projects/' + p.id, { method: 'DELETE' }); toast('Project dihapus'); render(); } catch (er) { handleErr(er); }
    };
    el.appendChild(col);
  }

  const drop = $('drop'), file = $('file');
  drop.onclick = () => file.click();
  drop.onkeydown = e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); drop.click(); } };
  file.onchange = () => { uploadTemplate(file.files[0]); file.value = ''; };
  ['dragenter', 'dragover'].forEach(t => drop.addEventListener(t, e => { e.preventDefault(); drop.classList.add('over'); }));
  ['dragleave', 'drop'].forEach(t => drop.addEventListener(t, e => { e.preventDefault(); drop.classList.remove('over'); }));
  drop.addEventListener('drop', e => uploadTemplate(e.dataTransfer.files[0]));
  $('tplBtn').onclick = () => bootstrap.Modal.getOrCreateInstance($('tplModal')).show();
  $('blankBtn').onclick = async () => {
    const c = document.createElement('canvas'); c.width = 2000; c.height = 1414;
    const x = c.getContext('2d'); x.fillStyle = '#fff'; x.fillRect(0, 0, 2000, 1414);
    await createProject('Sertifikat baru', c.toDataURL('image/png'), 2000, 1414, [nameField(2000, 1414, 'Playfair Display')]);
  };
}

const nameField = (w, h, font = 'Great Vibes', color = '#222222', y = 0.5) => ({
  id: 'f' + Date.now(), text: '{{Nama}}', x: Math.round(w / 2), y: Math.round(h * y), size: Math.round(w / 18),
  font, color, align: 'center', bold: false, italic: false, maxWidth: Math.round(w * 0.6),
});
async function createProject(name, image, w, h, fields = []) {
  try {
    const p = await api('/api/projects', { body: { name, image, width: w, height: h, fields, data: 'Nama\nBudi Santoso\nSiti Aminah\nAndi Wijaya' } });
    location.href = 'editor.html?id=' + encodeURIComponent(p.id);
  } catch (e) { handleErr(e); }
}
async function uploadTemplate(f) {
  if (!f) return;
  if (!/^image\/(jpeg|png|webp|svg\+xml)$/.test(f.type)) return toast('Format harus .jpg, .png, .webp, atau .svg', 1);
  if (f.size > 20e6) return toast('Ukuran file maksimal 20 MB', 1);
  $('dropText').textContent = 'Mengupload desain...';
  try {
    const url = await readAsDataURL(f);
    const img = await loadImage(url);
    const w = img.naturalWidth || 2000, h = img.naturalHeight || 1414;
    await createProject(f.name.replace(/\.[^.]+$/, ''), url, w, h, [nameField(w, h)]);
  } catch (e) { toast(e.message, 1); }
  if ($('dropText')) $('dropText').textContent = 'Upload desain sertifikat';
}

// Template bawaan (SVG)
function certSvg(bg, ac, title, style) {
  const orn = style === 'modern'
    ? `<rect width="800" height="70" fill="${ac}"/><rect y="496" width="800" height="70" fill="${ac}"/><circle cx="400" cy="70" r="34" fill="${bg}" stroke="${ac}" stroke-width="6"/>`
    : `<rect x="24" y="24" width="752" height="518" fill="none" stroke="${ac}" stroke-width="6"/><rect x="40" y="40" width="720" height="486" fill="none" stroke="${ac}" stroke-width="1.5"/><path d="M40 90 L90 40 M760 90 L710 40 M40 476 L90 526 M760 476 L710 526" stroke="${ac}" stroke-width="2"/>`;
  const fs = Math.min(46, Math.floor((600 / title.length - 6) / 0.72)); // judul panjang dikecilkan supaya muat
  return `<svg xmlns="http://www.w3.org/2000/svg" width="2000" height="1414" viewBox="0 0 800 566"><rect width="800" height="566" fill="${bg}"/>${orn}<text x="400" y="160" text-anchor="middle" font-family="Georgia,serif" font-size="${fs}" font-weight="bold" fill="${ac}" letter-spacing="6">${title}</text><text x="400" y="210" text-anchor="middle" font-family="Georgia,serif" font-size="17" fill="#555">diberikan kepada</text><line x1="220" y1="315" x2="580" y2="315" stroke="${ac}"/><text x="400" y="360" text-anchor="middle" font-family="Georgia,serif" font-size="15" fill="#555">atas partisipasi dan kontribusinya dalam kegiatan ini</text><line x1="130" y1="450" x2="290" y2="450" stroke="#555"/><line x1="510" y1="450" x2="670" y2="450" stroke="#555"/><text x="210" y="472" text-anchor="middle" font-family="Georgia,serif" font-size="13" fill="#555">Ketua Panitia</text><text x="590" y="472" text-anchor="middle" font-family="Georgia,serif" font-size="13" fill="#555">Kepala Sekolah</text></svg>`;
}
const svgUrl = s => 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(s)));
[
  ['Klasik Emas', '#fffdf5', '#b8912c', 'SERTIFIKAT', 'classic', '#3a2f12'],
  ['Biru Formal', '#f4f8ff', '#1d4ed8', 'PIAGAM PENGHARGAAN', 'classic', '#0f1d4a'],
  ['Hijau Modern', '#ffffff', '#2f7d3a', 'CERTIFICATE', 'modern', '#1f3d24'],
  ['Merah Elegan', '#fff7f7', '#a61e2f', 'SERTIFIKAT', 'classic', '#4a0d16'],
  ['Navy Modern', '#ffffff', '#1e2a4a', 'SERTIFIKAT', 'modern', '#1e2a4a'],
  ['Ungu Klasik', '#fbf8ff', '#6b3fa0', 'PENGHARGAAN', 'classic', '#2e1a47'],
].forEach(([n, bg, ac, title, style, ink]) => {
  const url = svgUrl(certSvg(bg, ac, title, style));
  const col = document.createElement('div'); col.className = 'col-6 col-md-4';
  col.innerHTML = `<div class="card sk-tpl h-100" role="button" tabindex="0"><img src="${url}" class="card-img-top" alt="Template ${esc(n)}"><div class="card-body p-2 text-center small">${esc(n)}</div></div>`;
  const pick = () => createProject(n, url, 2000, 1414, [nameField(2000, 1414, 'Great Vibes', ink, 0.52)]);
  col.firstElementChild.onclick = pick;
  col.firstElementChild.onkeydown = e => { if (e.key === 'Enter') pick(); };
  $('tplGrid').appendChild(col);
});

// Custom Fonts
async function viewFonts() {
  view.innerHTML = `<h2 class="h4 fw-bold mb-1">Custom Fonts</h2><p class="text-muted mb-4">Upload font sendiri (.ttf, .otf, .woff, .woff2) supaya bisa dipakai di editor sertifikat.</p>
    <form class="card card-body bg-light mb-4" id="fontForm"><div class="row g-3 align-items-end">
      <div class="col-md-5"><label class="form-label small fw-semibold" for="fontFile">File font</label><input type="file" class="form-control" id="fontFile" accept=".ttf,.otf,.woff,.woff2" required></div>
      <div class="col-md-5"><label class="form-label small fw-semibold" for="fontName">Nama font</label><input class="form-control" id="fontName" placeholder="mis. Tanda Tangan" required></div>
      <div class="col-md-2"><button class="btn btn-primary w-100" id="fontSubmit"><i class="bi bi-upload me-1"></i>Upload</button></div>
    </div></form>
    <div class="list-group" id="flist"></div>`;
  const list = await loadCustomFonts();
  $('flist').innerHTML = list.length ? '' : '<div class="list-group-item text-muted">Belum ada font custom.</div>';
  for (const f of list) {
    const d = document.createElement('div'); d.className = 'list-group-item d-flex align-items-center gap-3 py-3';
    d.innerHTML = `<div class="flex-grow-1 fs-4 text-truncate" style="font-family:'${esc(f.name)}'">Budi Santoso</div><small class="text-muted">${esc(f.name)}</small><button class="btn btn-outline-danger btn-sm">Hapus</button>`;
    d.querySelector('button').onclick = async () => {
      if (!(await confirmBox('Hapus font?', `Font "${f.name}" akan dihapus.`, 'Hapus'))) return;
      try { await api('/api/fonts/' + f.id, { method: 'DELETE' }); render(); } catch (e) { handleErr(e); }
    };
    $('flist').appendChild(d);
  }
  $('fontFile').onchange = () => { const f = $('fontFile').files[0]; if (f && !$('fontName').value) $('fontName').value = f.name.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' '); };
  $('fontForm').onsubmit = async e => {
    e.preventDefault();
    const f = $('fontFile').files[0]; if (!f) return;
    $('fontSubmit').disabled = true;
    try { await api('/api/fonts', { body: { name: $('fontName').value, filename: f.name, data: await readAsDataURL(f) } }); toast('Font ditambahkan'); render(); }
    catch (er) { handleErr(er); $('fontSubmit').disabled = false; }
  };
}

// Riwayat & Laporan
async function viewHistory() {
  const [{ list, stats }, projects] = await Promise.all([api('/api/generations'), api('/api/projects')]);
  const now = new Date();
  const thisMonth = list.filter(g => { const d = new Date(g.created_at); return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear(); })
    .reduce((n, g) => n + g.cert_count, 0);
  const fmtName = { pdf: 'PDF (1 file)', pdfzip: 'PDF per orang', pngzip: 'PNG', jpgzip: 'JPG' };
  const stat = (icon, label, value, sub = '') => `<div class="col-6 col-lg-3"><div class="card h-100"><div class="card-body">
    <div class="text-muted small"><i class="bi ${icon} me-1"></i>${label}</div><div class="fs-3 fw-bold mt-1">${value}</div><div class="small text-muted">${sub}</div></div></div></div>`;
  view.innerHTML = `<h2 class="h4 fw-bold mb-4">Riwayat &amp; Laporan</h2>
    <div class="row g-3 mb-4">
      ${stat('bi-calendar-month', 'Sertifikat bulan ini', thisMonth.toLocaleString('id-ID'))}
      ${stat('bi-award', 'Total sertifikat', stats.total.toLocaleString('id-ID'), 'sejak daftar')}
      ${stat('bi-lightning', 'Kali generate', stats.batches.toLocaleString('id-ID'))}
      ${stat('bi-folder2', 'Project', projects.length.toLocaleString('id-ID'))}
    </div>
    <div class="card"><div class="card-header bg-white fw-semibold">Riwayat generate</div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr><th>Tanggal</th><th>Project</th><th class="text-end">Jumlah</th><th>Format</th></tr></thead>
      <tbody>${list.length ? list.map(g => `<tr><td class="text-nowrap">${fmtDate(g.created_at, 1)}</td>
        <td>${g.project_id ? `<a href="editor.html?id=${encodeURIComponent(g.project_id)}">${esc(g.project_name)}</a>` : esc(g.project_name) + ' <span class="text-muted small">(dihapus)</span>'}</td>
        <td class="text-end fw-semibold">${g.cert_count}</td><td>${fmtName[g.format] || g.format}</td></tr>`).join('')
        : '<tr><td colspan="4" class="text-center text-muted py-4">Belum pernah generate sertifikat.</td></tr>'}</tbody>
    </table></div></div>`;
}

// mulai
(async () => {
  try { await refreshMe(); } catch (e) { toast(e.message, 1); }
  render();
  renderNotifs().catch(() => {});
})();
