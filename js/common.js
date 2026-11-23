// Fungsi bersama untuk semua halaman
async function api(path, opt = {}) {
  let r;
  try {
    r = await fetch(path, {
      method: opt.method || (opt.body ? 'POST' : 'GET'),
      headers: opt.body ? { 'Content-Type': 'application/json' } : {},
      body: opt.body ? JSON.stringify(opt.body) : undefined,
    });
  } catch {
    // fetch gagal total = tidak ada server yang menjawab (backend belum jalan, atau halaman dibuka dari file)
    throw new Error('Backend belum tersambung.');
  }
  let d = {};
  try { d = await r.json(); } catch {}
  if (r.status === 401 && !opt.noRedirect) { location.href = 'login.html'; throw new Error('Silakan login.'); }
  if (!r.ok) throw Object.assign(new Error(d.error || (r.status === 404 || r.status === 405 ? 'Backend belum tersambung.' : 'Terjadi kesalahan.')), { status: r.status });
  return d;
}

// Notifikasi kecil (Bootstrap toast)
function toast(msg, err) {
  let box = document.getElementById('toastBox');
  if (!box) {
    box = document.createElement('div');
    box.id = 'toastBox'; box.className = 'toast-container position-fixed bottom-0 start-50 translate-middle-x p-3';
    box.style.zIndex = 2000; document.body.appendChild(box);
  }
  const el = document.createElement('div');
  el.className = `toast align-items-center border-0 text-bg-${err ? 'danger' : 'dark'}`;
  el.setAttribute('role', 'status');
  el.innerHTML = `<div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button></div>`;
  el.querySelector('.toast-body').textContent = msg;
  box.appendChild(el);
  el.addEventListener('hidden.bs.toast', () => el.remove());
  new bootstrap.Toast(el, { delay: 3500 }).show();
}

const readAsDataURL = f => new Promise((ok, no) => { const r = new FileReader(); r.onload = () => ok(r.result); r.onerror = no; r.readAsDataURL(f); });
const loadImage = src => new Promise((ok, no) => { const i = new Image(); i.onload = () => ok(i); i.onerror = () => no(new Error('Gambar gagal dimuat.')); i.src = src; });
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const fmtDate = (d, time) => new Date(d).toLocaleString('id-ID', time ? { dateStyle: 'medium', timeStyle: 'short' } : { dateStyle: 'medium' });

// Muat font custom milik user supaya bisa dipakai di editor/canvas
async function loadCustomFonts() {
  const list = await api('/api/fonts');
  await Promise.all(list.map(async f => {
    try { const ff = new FontFace(f.name, `url(${f.url})`); await ff.load(); document.fonts.add(ff); } catch {}
  }));
  return list;
}
