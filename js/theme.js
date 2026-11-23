// Tema terang / gelap / ikut sistem.
// Dimuat di <head> supaya tema terpasang sebelum halaman tampil (tidak berkedip).
// Tombol pemilih tema otomatis dibuat di setiap elemen <div data-theme-toggle></div>.
(function () {
  const KEY = 'sk-theme';
  const media = matchMedia('(prefers-color-scheme: dark)');
  const get = () => { try { return localStorage.getItem(KEY) || 'auto'; } catch { return 'auto'; } };
  const isDark = m => m === 'dark' || (m === 'auto' && media.matches);
  const ICON = { light: 'bi-sun', dark: 'bi-moon-stars', auto: 'bi-circle-half' };
  const LABEL = { light: 'Terang', dark: 'Gelap', auto: 'Ikut sistem' };

  function apply(mode) {
    document.documentElement.setAttribute('data-bs-theme', isDark(mode) ? 'dark' : 'light');
    document.querySelectorAll('[data-theme-toggle]').forEach(render);
  }
  function set(mode) {
    try { localStorage.setItem(KEY, mode); } catch {}
    apply(mode);
  }
  function render(box) {
    const mode = get();
    box.innerHTML = `<div class="dropdown sk-theme">
      <button class="btn btn-light border btn-sm" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Tema: ${LABEL[mode]}" aria-label="Ganti tema (sekarang ${LABEL[mode]})">
        <i class="bi ${ICON[isDark(mode) ? 'dark' : 'light']}"></i></button>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        ${['light', 'dark', 'auto'].map(m => `<li><button class="dropdown-item ${m === mode ? 'active' : ''}" type="button" data-mode="${m}"><i class="bi ${ICON[m]} me-2"></i>${LABEL[m]}</button></li>`).join('')}
      </ul></div>`;
    box.querySelectorAll('[data-mode]').forEach(b => b.onclick = () => set(b.dataset.mode));
  }

  apply(get());
  media.addEventListener('change', () => { if (get() === 'auto') apply('auto'); });
  document.addEventListener('DOMContentLoaded', () => apply(get()));
  window.skTheme = { get, set };
})();
