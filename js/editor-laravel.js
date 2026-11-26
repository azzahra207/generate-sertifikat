// Editor sertifikat: atur teks, gambar, dan QR code di atas template, isi dari data, generate PDF/PNG/JPG
// Jenis elemen (field.type): 'text' (bawaan), 'image', 'qr'
const $ = id => document.getElementById(id);
// Pengaturan dari Blade (sertifikat/edit.blade.php). Selama rute backend belum ada,
// saveUrl kosong dan desain disimpan sebagai draf di browser (localStorage).
const CFG = window.MYSERTI_EDITOR || {};
const DRAFT_KEY = 'myserti-editor-draft';
const S = { p: null, me: null, fields: [], sel: null, rows: [], cols: [], prev: 0, W: 2000, H: 1414, s: 1, dirty: false, busy: false };
const BASE_FONTS = ['Arial', 'Georgia', 'Times New Roman', 'Courier New', 'Verdana', 'Trebuchet MS'];
const GOOGLE_FONTS = ['Great Vibes', 'Pinyon Script', 'Dancing Script', 'Playfair Display', 'Lora', 'Cinzel', 'Montserrat', 'Poppins'];
let customFonts = [];
const mctx = document.createElement('canvas').getContext('2d');
const genModal = new bootstrap.Modal($('genModal'));
const T = f => f.type || 'text';
const newId = () => 'f' + Date.now() + Math.random().toString(36).slice(2, 6);
if (window.qrcode) qrcode.stringToBytes = qrcode.stringToBytesFuncs['UTF-8']; // dukung huruf non-ASCII

// teks & data
const fontStr = (f, size) => `${f.italic ? 'italic ' : ''}${f.bold ? 'bold ' : ''}${size}px "${f.font}"`;
function fill(text, row, i) {
  return String(text).replace(/\{\{\s*([^}]+?)\s*\}\}/g, (m, k) => {
    if (k.toLowerCase() === 'no') return row ? String(i + 1) : m;
    if (!row) return m;
    const key = Object.keys(row).find(c => c.toLowerCase() === k.toLowerCase());
    return key ? row[key] : '';
  });
}
function effSize(f, txt) {
  if (!(f.maxWidth > 0)) return f.size;
  mctx.font = fontStr(f, f.size);
  const w = Math.max(...txt.split('\n').map(l => mctx.measureText(l).width));
  return w > f.maxWidth ? Math.max(4, Math.floor(f.size * f.maxWidth / w)) : f.size;
}
function parseCSV(t) {
  t = t.replace(/^\uFEFF/, '');
  const first = t.split(/\r?\n/)[0] || '';
  const d = first.includes('\t') ? '\t' : (first.split(';').length > first.split(',').length ? ';' : ',');
  const rows = []; let row = [], cur = '', q = false;
  for (let i = 0; i < t.length; i++) {
    const c = t[i];
    if (q) { if (c === '"') { if (t[i + 1] === '"') { cur += '"'; i++; } else q = false; } else cur += c; }
    else if (c === '"' && cur === '') q = true;
    else if (c === d) { row.push(cur); cur = ''; }
    else if (c === '\n' || c === '\r') { if (c === '\r' && t[i + 1] === '\n') i++; row.push(cur); rows.push(row); row = []; cur = ''; }
    else cur += c;
  }
  if (cur !== '' || row.length) { row.push(cur); rows.push(row); }
  return rows.map(r => r.map(x => x.trim()));
}
function updateData() {
  const all = parseCSV($('dataText').value).filter(r => r.some(x => x));
  S.cols = (all[0] || []).map((c, i) => c || 'Kolom' + (i + 1));
  S.rows = all.slice(1).map(r => Object.fromEntries(S.cols.map((c, i) => [c, r[i] ?? ''])));
  if (S.prev >= S.rows.length) S.prev = Math.max(0, S.rows.length - 1);
  $('rowCount').textContent = S.rows.length || '';
  const chips = list => list.map(c => `<span class="chip" role="button" title="Sisipkan ke elemen terpilih">{{${esc(c)}}}</span>`).join('');
  $('colChips').innerHTML = chips(S.cols);
  $('pChips').innerHTML = $('pQrChips').innerHTML = chips(S.cols.concat(['no']));
  document.querySelectorAll('#colChips .chip, #pChips .chip, #pQrChips .chip').forEach(ch => ch.onclick = () => insertToken(ch.textContent));
  $('dtable').innerHTML = S.rows.length
    ? `<table class="table table-sm table-hover small mb-0"><thead class="table-light"><tr><th>#</th>${S.cols.map(c => `<th>${esc(c)}</th>`).join('')}</tr></thead><tbody>${S.rows.slice(0, 500).map((r, i) =>
        `<tr data-i="${i}" class="${i === S.prev ? 'on' : ''}"><td>${i + 1}</td>${S.cols.map(c => `<td class="text-nowrap">${esc(r[c])}</td>`).join('')}</tr>`).join('')}</tbody></table>`
    : '<div class="text-muted small p-2">Belum ada baris data.</div>';
  $('dtable').querySelectorAll('tbody tr').forEach(tr => tr.onclick = () => { S.prev = +tr.dataset.i; updateData(); });
  renderStage(); renderList();
}
function insertToken(tok) {
  const f = cur();
  if (!f || T(f) === 'image') return toast('Pilih elemen teks atau QR code dulu', 1);
  const ta = T(f) === 'qr' ? $('pQrText') : $('pText');
  const a = ta.selectionStart ?? ta.value.length, b = ta.selectionEnd ?? a;
  f.text = ta.value.slice(0, a) + tok + ta.value.slice(b);
  ta.value = f.text; changed(); switchTab('text');
}

// QR code
function qrMake(text) {
  const q = qrcode(0, 'M');
  q.addData(text || ' '); q.make();
  return q;
}
function qrSvg(text, color, bg) {
  let q;
  try { q = qrMake(text); } catch { return '<div class="w-100 h-100 d-grid text-center small text-danger bg-white" style="place-items:center">Isi QR terlalu panjang</div>'; }
  const n = q.getModuleCount(); let d = '';
  for (let r = 0; r < n; r++) for (let c = 0; c < n; c++) if (q.isDark(r, c)) d += `M${c + 2},${r + 2}h1v1h-1z`;
  return `<svg viewBox="0 0 ${n + 4} ${n + 4}" shape-rendering="crispEdges">${bg ? `<rect width="${n + 4}" height="${n + 4}" fill="#fff"/>` : ''}<path d="${d}" fill="${esc(color)}"/></svg>`;
}
function drawQr(x, f, text) {
  const q = qrMake(text), n = q.getModuleCount(), cell = f.size / (n + 4);
  const x0 = f.x - f.size / 2, y0 = f.y - f.size / 2;
  if (f.bg) { x.fillStyle = '#fff'; x.fillRect(x0, y0, f.size, f.size); }
  x.fillStyle = f.color;
  for (let r = 0; r < n; r++) for (let c = 0; c < n; c++)
    if (q.isDark(r, c)) x.fillRect(x0 + (c + 2) * cell, y0 + (r + 2) * cell, cell + 0.5, cell + 0.5);
}

// gambar
const imgCache = new Map();
const getImg = src => { if (!imgCache.has(src)) imgCache.set(src, loadImage(src)); return imgCache.get(src); };
function pickImage() {
  return new Promise(ok => {
    const inp = $('imgFile');
    inp.onchange = async () => {
      const f = inp.files[0]; inp.value = '';
      if (!f) return;
      if (!/^image\/(png|jpeg|webp|svg\+xml)$/.test(f.type)) return toast('Format gambar harus .png, .jpg, .webp, atau .svg', 1);
      if (f.size > 10e6) return toast('Ukuran gambar maksimal 10 MB', 1);
      try {
        let url = await readAsDataURL(f);
        const img = await loadImage(url);
        let w = img.naturalWidth || 400, h = img.naturalHeight || 400;
        // perkecil gambar besar supaya project tetap ringan
        if (f.type !== 'image/svg+xml' && (Math.max(w, h) > 1200 || f.size > 800e3)) {
          const k = Math.min(1, 1200 / Math.max(w, h));
          const c = document.createElement('canvas'); c.width = Math.round(w * k); c.height = Math.round(h * k);
          c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
          url = c.toDataURL(f.type === 'image/jpeg' ? 'image/jpeg' : 'image/png', 0.9); w = c.width; h = c.height;
        }
        ok({ url, w, h });
      } catch (e) { toast(e.message, 1); }
    };
    inp.click();
  });
}

// panggung
const cur = () => S.fields.find(f => f.id === S.sel);
// S.zoom kosong = pas layar; selain itu skala tetap pilihan user
function fitStage() {
  const wrap = $('stageWrap'), pad = window.innerWidth < 860 ? 24 : 56;
  S.fit = Math.min((wrap.clientWidth - pad) / S.W, (wrap.clientHeight - pad) / S.H, 1);
  S.s = S.zoom ?? S.fit;
  $('stage').style.width = S.W * S.s + 'px'; $('stage').style.height = S.H * S.s + 'px';
  $('sizeLabel').textContent = `${S.W} × ${S.H} px`;
  $('zoomLabel').textContent = Math.round(S.s * 100) + '%';
  $('zoomFit').classList.toggle('active', S.zoom == null);
  renderStage();
}

// zoom
const ZOOMS = [0.1, 0.15, 0.2, 0.25, 0.33, 0.5, 0.67, 0.75, 1, 1.25, 1.5, 2, 3];
function setZoom(z, anchor) {
  const wrap = $('stageWrap'), wr = wrap.getBoundingClientRect(), r = $('stage').getBoundingClientRect();
  // titik yang dijaga tetap di tempat (posisi kursor, atau tengah layar)
  const ax = anchor ? anchor.x : wr.left + wrap.clientWidth / 2, ay = anchor ? anchor.y : wr.top + wrap.clientHeight / 2;
  const px = (ax - r.left) / S.s, py = (ay - r.top) / S.s;
  S.zoom = z == null ? null : Math.max(0.05, Math.min(3, z));
  fitStage();
  if (S.zoom != null) {
    const r2 = $('stage').getBoundingClientRect();
    wrap.scrollLeft += r2.left + px * S.s - ax;
    wrap.scrollTop += r2.top + py * S.s - ay;
  }
}
function zoomStep(dir) {
  const next = dir > 0 ? ZOOMS.find(z => z > S.s + 0.001) ?? 3 : [...ZOOMS].reverse().find(z => z < S.s - 0.001) ?? 0.05;
  setZoom(next);
}
$('zoomIn').onclick = () => zoomStep(1);
$('zoomOut').onclick = () => zoomStep(-1);
$('zoomLabel').onclick = () => setZoom(1);
$('zoomFit').onclick = () => setZoom(null);
$('stageWrap').addEventListener('wheel', e => {
  if (!e.ctrlKey && !e.metaKey) return; // scroll biasa tetap menggeser kanvas
  e.preventDefault();
  setZoom(S.s * (e.deltaY < 0 ? 1.15 : 1 / 1.15), { x: e.clientX, y: e.clientY });
}, { passive: false });
function placeEl(el, f) {
  const t = T(f);
  el.style.left = f.x * S.s + 'px'; el.style.top = f.y * S.s + 'px';
  if (t === 'image') {
    const inner = el.firstElementChild;
    if (!inner.firstElementChild) { const im = new Image(); im.src = f.src; im.alt = ''; inner.appendChild(im); }
    inner.style.opacity = f.opacity ?? 1;
    Object.assign(el.style, { width: f.w * S.s + 'px', height: f.w / f.ratio * S.s + 'px', transform: 'translate(-50%,-50%)' });
    return;
  }
  if (t === 'qr') {
    const txt = fill(f.text, S.rows[S.prev], S.prev), key = txt + '|' + f.color + '|' + f.bg;
    if (el.dataset.k !== key) { el.firstElementChild.innerHTML = qrSvg(txt, f.color, f.bg); el.dataset.k = key; }
    Object.assign(el.style, { width: f.size * S.s + 'px', height: f.size * S.s + 'px', transform: 'translate(-50%,-50%)' });
    return;
  }
  const txt = fill(f.text, S.rows[S.prev], S.prev) || ' ';
  el.textContent = txt;
  Object.assign(el.style, {
    fontSize: effSize(f, txt) * S.s + 'px', fontFamily: `"${f.font}"`,
    color: f.color, fontWeight: f.bold ? 700 : 400, fontStyle: f.italic ? 'italic' : 'normal', textAlign: f.align,
    transform: `translate(${f.align === 'left' ? 0 : f.align === 'right' ? -100 : -50}%,-50%)`,
  });
}
function renderStage() {
  const st = $('stage');
  st.querySelectorAll('.fld').forEach(e => e.remove());
  for (const f of S.fields) {
    const el = document.createElement('div'), box = T(f) !== 'text', on = f.id === S.sel;
    el.className = 'fld' + (box ? ' fld-box' : '') + (on ? ' on' : ''); el.dataset.id = f.id;
    if (box) el.innerHTML = '<div class="inner"></div>' + (on ? '<div class="rh" title="Tarik untuk mengubah ukuran"></div>' : '');
    placeEl(el, f); st.appendChild(el);
  }
  $('rowLabel').textContent = S.rows.length ? `${S.prev + 1} / ${S.rows.length}` : 'tanpa data';
}
function renderList() {
  $('flist').innerHTML = S.fields.length ? '' : '<div class="text-muted small">Belum ada elemen. Tambahkan teks, gambar, atau QR code.</div>';
  for (const f of S.fields) {
    const t = T(f), d = document.createElement('div');
    d.className = 'fi border rounded px-2 py-2 d-flex align-items-center gap-2 small' + (f.id === S.sel ? ' active' : '');
    const icon = { text: 'bi-fonts', image: 'bi-image', qr: 'bi-qr-code' }[t];
    const meta = { text: esc(f.font), image: 'Gambar', qr: 'QR code' }[t];
    d.innerHTML = `<i class="bi ${icon}"></i><span class="flex-grow-1 text-truncate"></span><span class="text-muted" style="font-size:11px">${meta}</span>`;
    d.querySelector('span').textContent = t === 'image' ? `Gambar ${f.w}px` : (f.text.replace(/\n/g, ' ') || '(kosong)');
    d.onclick = () => select(f.id);
    $('flist').appendChild(d);
  }
}
function renderProps() {
  const f = cur();
  $('props').classList.toggle('d-none', !f);
  if (!f) return;
  const t = T(f);
  $('boxText').classList.toggle('d-none', t !== 'text');
  $('boxImg').classList.toggle('d-none', t !== 'image');
  $('boxQr').classList.toggle('d-none', t !== 'qr');
  $('pX').value = Math.round(f.x); $('pY').value = Math.round(f.y);
  if (t === 'text') {
    $('pText').value = f.text; $('pFont').value = f.font; $('pSize').value = f.size; $('pColor').value = f.color; $('pMax').value = f.maxWidth || 0;
    document.querySelectorAll('#pAlign button').forEach(b => b.classList.toggle('active', b.dataset.v === f.align));
    $('pBold').classList.toggle('active', !!f.bold); $('pItalic').classList.toggle('active', !!f.italic);
  } else if (t === 'image') {
    $('pImgPrev').src = f.src; $('pImgW').value = Math.round(f.w);
    $('pOpacity').value = Math.round((f.opacity ?? 1) * 100); $('pOpacityVal').textContent = $('pOpacity').value + '%';
  } else {
    $('pQrText').value = f.text; $('pQrSize').value = Math.round(f.size); $('pQrColor').value = f.color; $('pQrBg').checked = !!f.bg;
  }
}
function select(id) { S.sel = id; renderStage(); renderList(); renderProps(); if (id) switchTab('text'); }
function changed() { renderStage(); renderList(); markDirty(); }

// geser & ubah ukuran dengan mouse/jari
let drag = null;
$('stage').addEventListener('pointerdown', e => {
  const el = e.target.closest('.fld');
  if (!el) { select(null); return; }
  const f = S.fields.find(x => x.id === el.dataset.id);
  const resize = e.target.classList.contains('rh');
  if (S.sel !== f.id) select(f.id);
  drag = { f, el: $('stage').querySelector(`.fld[data-id="${f.id}"]`), sx: e.clientX, sy: e.clientY, fx: f.x, fy: f.y,
    mode: resize ? 'resize' : 'move', sw: T(f) === 'image' ? f.w : f.size, moved: false };
  $('stage').setPointerCapture(e.pointerId);
  e.preventDefault();
});
$('stage').addEventListener('pointermove', e => {
  if (!drag) return;
  const dx = (e.clientX - drag.sx) / S.s, dy = (e.clientY - drag.sy) / S.s;
  if (Math.abs(dx) + Math.abs(dy) > 1) drag.moved = true;
  const f = drag.f;
  if (drag.mode === 'resize') {
    const n = Math.max(20, Math.round(drag.sw + Math.max(dx, dy) * 2));
    if (T(f) === 'image') { f.w = n; $('pImgW').value = n; } else { f.size = n; $('pQrSize').value = n; }
    placeEl(drag.el, f);
    return;
  }
  let x = Math.round(drag.fx + dx); const y = Math.round(drag.fy + dy);
  const snap = Math.abs(x - S.W / 2) < 8 / S.s && (T(f) !== 'text' || f.align === 'center');
  if (snap) x = Math.round(S.W / 2);
  $('vguide').style.display = snap ? 'block' : 'none'; $('vguide').style.left = S.W / 2 * S.s + 'px';
  f.x = Math.max(0, Math.min(S.W, x)); f.y = Math.max(0, Math.min(S.H, y));
  placeEl(drag.el, f);
  $('pX').value = f.x; $('pY').value = f.y;
});
['pointerup', 'pointercancel'].forEach(t => $('stage').addEventListener(t, () => {
  if (drag?.moved) { if (drag.mode === 'resize') renderList(); markDirty(); }
  drag = null; $('vguide').style.display = 'none';
}));

// panel properti
function bind(id, key, conv = v => v, ev = 'input') {
  $(id).addEventListener(ev, () => { const f = cur(); if (!f) return; f[key] = conv($(id)); changed(); });
}
const val = el => el.value;
bind('pText', 'text', val); bind('pFont', 'font', val); bind('pColor', 'color', val);
bind('pSize', 'size', el => Math.max(4, Math.min(2000, +el.value || 4)));
bind('pX', 'x', el => +el.value || 0); bind('pY', 'y', el => +el.value || 0);
bind('pMax', 'maxWidth', el => Math.max(0, +el.value || 0));
bind('pImgW', 'w', el => Math.max(10, Math.min(S.W * 2, +el.value || 10)));
bind('pOpacity', 'opacity', el => { $('pOpacityVal').textContent = el.value + '%'; return Math.max(0.1, Math.min(1, el.value / 100)); });
bind('pQrText', 'text', val); bind('pQrColor', 'color', val);
bind('pQrSize', 'size', el => Math.max(40, Math.min(S.W, +el.value || 40)));
bind('pQrBg', 'bg', el => el.checked, 'change');
document.querySelectorAll('#pAlign button').forEach(b => b.onclick = () => { cur().align = b.dataset.v; renderProps(); changed(); });
$('pBold').onclick = () => { const f = cur(); f.bold = !f.bold; renderProps(); changed(); };
$('pItalic').onclick = () => { const f = cur(); f.italic = !f.italic; renderProps(); changed(); };
$('centerBtn').onclick = () => { const f = cur(); if (T(f) === 'text') f.align = 'center'; f.x = Math.round(S.W / 2); renderProps(); changed(); };
function moveLayer(dir) {
  const i = S.fields.findIndex(f => f.id === S.sel), j = i + dir;
  if (i < 0 || j < 0 || j >= S.fields.length) return;
  [S.fields[i], S.fields[j]] = [S.fields[j], S.fields[i]]; changed();
}
$('upBtn').onclick = () => moveLayer(1);
$('downBtn').onclick = () => moveLayer(-1);
$('dupBtn').onclick = () => {
  const f = cur(), h = T(f) === 'text' ? f.size * 1.4 : (T(f) === 'image' ? f.w / f.ratio : f.size) + 20;
  const n = { ...f, id: newId(), y: Math.min(S.H, f.y + h) };
  S.fields.push(n); select(n.id); markDirty();
};
$('delBtn').onclick = () => { S.fields = S.fields.filter(x => x.id !== S.sel); select(null); markDirty(); };
$('addBtn').onclick = () => {
  const n = { id: newId(), type: 'text', text: S.cols[0] ? `{{${S.cols[0]}}}` : 'Teks baru', x: Math.round(S.W / 2), y: Math.round(S.H / 2),
    size: Math.round(S.W / 30), font: 'Montserrat', color: '#222222', align: 'center', bold: false, italic: false, maxWidth: 0 };
  S.fields.push(n); select(n.id); markDirty();
};
$('addImgBtn').onclick = async () => {
  const im = await pickImage();
  const w = Math.round(Math.min(S.W * 0.18, im.w * 2));
  const n = { id: newId(), type: 'image', src: im.url, ratio: im.w / im.h, w, x: Math.round(S.W * 0.82), y: Math.round(S.H * 0.78), opacity: 1 };
  S.fields.push(n); select(n.id); markDirty();
};
$('pImgChange').onclick = async () => {
  const f = cur(); const im = await pickImage();
  f.src = im.url; f.ratio = im.w / im.h; renderProps(); changed();
};
$('addQrBtn').onclick = () => {
  const size = Math.round(Math.min(S.W, S.H) * 0.16);
  const n = { id: newId(), type: 'qr', text: S.cols[0] ? `{{${S.cols[0]}}}` : 'https://contoh.com', size,
    x: Math.round(size / 2 + S.W * 0.06), y: Math.round(S.H - size / 2 - S.H * 0.08), color: '#000000', bg: true };
  S.fields.push(n); select(n.id); markDirty();
};
function fillFontSelect() {
  const opt = l => l.map(f => `<option value="${esc(f)}" style="font-family:'${esc(f)}'">${esc(f)}</option>`).join('');
  $('pFont').innerHTML = `<optgroup label="Script, Serif & Sans">${opt(GOOGLE_FONTS)}</optgroup><optgroup label="Font sistem">${opt(BASE_FONTS)}</optgroup>`
    + (customFonts.length ? `<optgroup label="Font custom kamu">${opt(customFonts.map(f => f.name))}</optgroup>` : '');
}

// keyboard
document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') { e.preventDefault(); saveNow(); return; }
  if ((e.ctrlKey || e.metaKey) && ['=', '+', '-', '0'].includes(e.key)) {
    e.preventDefault();
    if (e.key === '0') setZoom(null); else zoomStep(e.key === '-' ? -1 : 1);
    return;
  }
  if (/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName) || !cur() || document.querySelector('.modal.show')) return;
  const f = cur(), st = e.shiftKey ? 10 : 1;
  const mv = { ArrowLeft: [-st, 0], ArrowRight: [st, 0], ArrowUp: [0, -st], ArrowDown: [0, st] }[e.key];
  if (mv) { e.preventDefault(); f.x += mv[0]; f.y += mv[1]; renderProps(); changed(); }
  if (e.key === 'Delete' || e.key === 'Backspace') { e.preventDefault(); $('delBtn').click(); }
});

// tab & data
function switchTab(t) {
  document.querySelectorAll('[data-tab]').forEach(b => b.classList.toggle('active', b.dataset.tab === t));
  $('t-text').classList.toggle('d-none', t !== 'text'); $('t-data').classList.toggle('d-none', t !== 'data');
}
document.querySelectorAll('[data-tab]').forEach(b => b.onclick = () => switchTab(b.dataset.tab));
$('dataText').addEventListener('input', () => { updateData(); markDirty(); });
$('csvBtn').onclick = () => $('csvFile').click();
$('csvFile').onchange = async () => {
  const f = $('csvFile').files[0]; if (!f) return;
  $('dataText').value = await f.text(); $('csvFile').value = ''; S.prev = 0; updateData(); markDirty();
  toast(`${S.rows.length} baris data dimuat`);
};
$('clearBtn').onclick = () => { if (confirm('Kosongkan semua data?')) { $('dataText').value = ''; updateData(); markDirty(); } };
$('prevRow').onclick = () => { if (S.prev > 0) { S.prev--; updateData(); } };
$('nextRow').onclick = () => { if (S.prev < S.rows.length - 1) { S.prev++; updateData(); } };

// simpan otomatis
let saveTimer;
function markDirty() { S.dirty = true; $('status').textContent = 'Menyimpan...'; clearTimeout(saveTimer); saveTimer = setTimeout(saveNow, 700); }
async function saveNow() {
  clearTimeout(saveTimer);
  const body = { name: $('pname').value, width: S.W, height: S.H, image: $('bg').src, fields: S.fields, data: $('dataText').value };
  try {
    if (CFG.saveUrl) {
      // ke backend Laravel (rute dari tim Backend)
      const r = await fetch(CFG.saveUrl, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CFG.csrf },
        body: JSON.stringify(body),
      });
      if (!r.ok) throw new Error('Gagal menyimpan ke server (' + r.status + ').');
      $('status').innerHTML = '<i class="bi bi-check2"></i> Tersimpan';
    } else {
      // sementara: draf di browser
      try { localStorage.setItem(DRAFT_KEY, JSON.stringify(body)); }
      catch { throw new Error('Draf terlalu besar untuk disimpan di browser. Kecilkan gambar desain.'); }
      $('status').innerHTML = '<i class="bi bi-check2"></i> Draf tersimpan di browser';
    }
    S.dirty = false;
  } catch (e) { $('status').textContent = 'Gagal menyimpan'; toast(e.message, 1); }
}

// ganti gambar desain (latar sertifikat)
$('bgBtn').onclick = () => $('bgFile').click();
$('bgFile').onchange = async () => {
  const f = $('bgFile').files[0]; $('bgFile').value = '';
  if (!f) return;
  if (!/^image\/(png|jpeg|webp|svg\+xml)$/.test(f.type)) return toast('Format desain harus .png, .jpg, .webp, atau .svg', 1);
  try {
    const img = await loadImage(await readAsDataURL(f));
    let w = img.naturalWidth || 2000, h = img.naturalHeight || 1414;
    const k = Math.min(1, 2400 / Math.max(w, h)); // batasi ukuran supaya ringan
    w = Math.round(w * k); h = Math.round(h * k);
    const c = document.createElement('canvas'); c.width = w; c.height = h;
    c.getContext('2d').drawImage(img, 0, 0, w, h);
    const url = c.toDataURL('image/jpeg', 0.9);
    // posisi elemen ikut diskalakan ke ukuran desain baru
    const sx = w / S.W, sy = h / S.H;
    S.fields.forEach(fl => { fl.x = Math.round(fl.x * sx); fl.y = Math.round(fl.y * sy); });
    S.W = w; S.H = h; S.zoom = null;
    await setBg(url);
    fitStage(); renderList(); renderProps(); markDirty();
    toast('Desain diganti');
  } catch (e) { toast(e.message, 1); }
};
function setBg(src) {
  return new Promise((ok, no) => { $('bg').onload = ok; $('bg').onerror = () => no(new Error('Gambar desain gagal dimuat.')); $('bg').src = src; });
}
// desain kosong bawaan: kertas putih dengan bingkai
function blankDesign(w, h) {
  const c = document.createElement('canvas'); c.width = w; c.height = h;
  const x = c.getContext('2d');
  x.fillStyle = '#ffffff'; x.fillRect(0, 0, w, h);
  x.strokeStyle = '#0f766e'; x.lineWidth = 16; x.strokeRect(40, 40, w - 80, h - 80);
  x.strokeStyle = '#f59e0b'; x.lineWidth = 4; x.strokeRect(72, 72, w - 144, h - 144);
  return c.toDataURL('image/png');
}
$('pname').addEventListener('input', markDirty);
window.addEventListener('beforeunload', e => { if (S.dirty) { saveNow(); e.preventDefault(); e.returnValue = ''; } });

// ringkasan generate
const count = () => S.rows.length || 1;
function genSummary() {
  $('gSum').innerHTML = `Jumlah sertifikat: <b>${count()}</b> · Ukuran: <b>${S.W} × ${S.H} px</b>`;
  $('gGo').disabled = S.busy;
}
$('genBtn').onclick = () => {
  if (!S.fields.length) return toast('Tambahkan elemen dulu', 1);
  const hasToken = S.fields.some(f => T(f) !== 'image' && /\{\{(?!\s*no\s*\}\})[^}]+\}\}/i.test(f.text));
  if (hasToken && !S.rows.length) { switchTab('data'); return toast('Isi data dulu di tab Data', 1); }
  if (!$('gName').value) $('gName').value = S.cols[0] ? `{{${S.cols[0]}}}` : ($('pname').value || 'sertifikat');
  $('gProg').classList.add('d-none'); $('gMsg').textContent = '';
  genSummary(); genModal.show();
};

// render & download
async function renderCert(row, i) {
  const c = document.createElement('canvas'); c.width = S.W; c.height = S.H;
  const x = c.getContext('2d');
  x.fillStyle = '#fff'; x.fillRect(0, 0, S.W, S.H);
  x.drawImage($('bg'), 0, 0, S.W, S.H);
  for (const f of S.fields) {
    const t = T(f);
    if (t === 'image') {
      const im = await getImg(f.src), h = f.w / f.ratio;
      x.globalAlpha = f.opacity ?? 1; x.drawImage(im, f.x - f.w / 2, f.y - h / 2, f.w, h); x.globalAlpha = 1;
      continue;
    }
    const txt = fill(f.text, row, i); if (!txt) continue;
    if (t === 'qr') { drawQr(x, f, txt); continue; }
    const size = effSize(f, txt), lh = size * 1.2, lines = txt.split('\n');
    x.font = fontStr(f, size); x.fillStyle = f.color; x.textAlign = f.align; x.textBaseline = 'middle';
    let y = f.y - (lines.length - 1) * lh / 2;
    for (const l of lines) { x.fillText(l, f.x, y); y += lh; }
  }
  return c;
}
function fileName(row, i, ext, used) {
  const n = fill($('gName').value || 'sertifikat', row, i).replace(/[\\/:*?"<>|\r\n]+/g, '-').trim().slice(0, 80) || `sertifikat-${i + 1}`;
  let out = n + ext, k = 2;
  while (used.has(out.toLowerCase())) out = `${n} (${k++})${ext}`;
  used.add(out.toLowerCase()); return out;
}
const canvasBlob = (c, type) => new Promise(ok => c.toBlob(ok, type, 0.92));
function download(blob, name) {
  const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = name;
  document.body.appendChild(a); a.click(); a.remove(); setTimeout(() => URL.revokeObjectURL(a.href), 60000);
}
$('gGo').onclick = async () => {
  const fmt = $('gFormat').value, rows = S.rows.length ? S.rows : [null], n = rows.length;
  if ((fmt.startsWith('pdf') && !window.jspdf) || (fmt.endsWith('zip') && n > 1 && !window.JSZip))
    return toast('Library PDF/ZIP gagal dimuat. Pastikan terhubung ke internet lalu muat ulang.', 1);
  if (S.fields.some(f => T(f) === 'qr') && !window.qrcode) return toast('Library QR code gagal dimuat. Muat ulang halaman.', 1);
  S.busy = true; $('gGo').disabled = true; $('genClose').disabled = true; $('gProg').classList.remove('d-none');
  const bar = $('gProg').firstElementChild, used = new Set();
  try {
    await saveNow();
    $('gMsg').textContent = 'Menyiapkan font...';
    await Promise.all(S.fields.filter(f => T(f) === 'text').map(f => document.fonts.load(fontStr(f, 40), 'Aa')));
    const orient = S.W >= S.H ? 'landscape' : 'portrait';
    const newPdf = () => new window.jspdf.jsPDF({ orientation: orient, unit: 'px', format: [S.W, S.H], hotfixes: ['px_scaling'], compress: true });
    const pdf = fmt === 'pdf' ? newPdf() : null;
    const outs = [];
    for (let i = 0; i < n; i++) {
      $('gMsg').textContent = `Membuat sertifikat ${i + 1} dari ${n}...`;
      const c = await renderCert(rows[i], i);
      if (fmt === 'pdf') {
        if (i) pdf.addPage([S.W, S.H], orient);
        pdf.addImage(c.toDataURL('image/jpeg', 0.92), 'JPEG', 0, 0, S.W, S.H, undefined, 'FAST');
      } else if (fmt === 'pdfzip') {
        const p = newPdf(); p.addImage(c.toDataURL('image/jpeg', 0.92), 'JPEG', 0, 0, S.W, S.H, undefined, 'FAST');
        outs.push([fileName(rows[i], i, '.pdf', used), p.output('blob')]);
      } else {
        const png = fmt === 'pngzip';
        outs.push([fileName(rows[i], i, png ? '.png' : '.jpg', used), await canvasBlob(c, png ? 'image/png' : 'image/jpeg')]);
      }
      bar.style.width = Math.round((i + 1) / n * 90) + '%';
      await new Promise(r => setTimeout(r));
    }
    // catat ke riwayat di backend (kalau gagal, download tetap jalan)
    if (CFG.historyUrl) {
      fetch(CFG.historyUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CFG.csrf },
        body: JSON.stringify({ count: n, format: fmt }) }).catch(() => {});
    }
    $('gMsg').textContent = 'Menyiapkan download...';
    const base = ($('pname').value || 'sertifikat').replace(/[\\/:*?"<>|]+/g, '-');
    if (fmt === 'pdf') download(pdf.output('blob'), base + '.pdf');
    else if (n > 1) { const zip = new JSZip(); outs.forEach(([nm, b]) => zip.file(nm, b)); download(await zip.generateAsync({ type: 'blob' }), base + '.zip'); }
    else download(outs[0][1], outs[0][0]);
    bar.style.width = '100%'; $('gMsg').textContent = `Selesai! ${n} sertifikat dibuat.`;
    toast('Download dimulai');
  } catch (e) {
    $('gMsg').textContent = e.message;
    toast(e.message, 1);
  }
  S.busy = false; $('genClose').disabled = false; genSummary();
};

// mulai
(async () => {
  try {
    // urutan sumber data: dari backend (Blade) -> draf di browser -> desain kosong
    let p = CFG.project;
    if (!p) { try { p = JSON.parse(localStorage.getItem(DRAFT_KEY)); } catch { p = null; } }
    if (!p) {
      const W = 2000, H = 1414;
      p = { name: 'Sertifikat baru', width: W, height: H, image: blankDesign(W, H), data: 'Nama\nBudi Santoso\nSiti Aminah',
        fields: [{ id: newId(), type: 'text', text: '{{Nama}}', x: W / 2, y: Math.round(H * 0.5), size: Math.round(W / 18),
          font: 'Great Vibes', color: '#222222', align: 'center', bold: false, italic: false, maxWidth: Math.round(W * 0.6) }] };
    }
    S.p = p; S.W = p.width; S.H = p.height; S.fields = p.fields || [];
    $('pname').value = p.name || 'Sertifikat baru'; document.title = $('pname').value + ' | MySerti';
    $('dataText').value = p.data || '';
    if (CFG.fontsUrl) customFonts = await loadCustomFonts().catch(() => []);
    fillFontSelect();
    await setBg(p.image);
    updateData(); fitStage(); renderList();
    $('status').innerHTML = CFG.saveUrl ? '<i class="bi bi-check2"></i> Tersimpan' : '<i class="bi bi-info-circle"></i> Mode draf (disimpan di browser)';
    window.addEventListener('resize', fitStage);
    document.fonts.addEventListener('loadingdone', renderStage);
  } catch (e) { toast(e.message, 1); }
})();
