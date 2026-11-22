// WidaraHub — JS publik (menu mobile, aksesibilitas, lightbox). Ganti/tambahkan sesuai kebutuhan UI Anda.
(function () {
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var store = {
    get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) {} }
  };

  // Menu mobile
  var toggle = $('#navToggle'), nav = $('#nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Aksesibilitas: ukuran teks & kontras
  var root = document.documentElement;
  var size = parseInt(store.get('wh_font') || '100', 10);
  var applySize = function () { root.style.fontSize = size + '%'; store.set('wh_font', String(size)); };
  if (size !== 100) applySize();
  if (store.get('wh_contrast') === '1') root.classList.add('hc');
  document.querySelectorAll('[data-a11y]').forEach(function (b) {
    b.addEventListener('click', function () {
      var a = b.getAttribute('data-a11y');
      if (a === 'font-up') { size = Math.min(150, size + 10); applySize(); }
      if (a === 'font-down') { size = Math.max(80, size - 10); applySize(); }
      if (a === 'contrast') { store.set('wh_contrast', root.classList.toggle('hc') ? '1' : '0'); }
    });
  });

  // Lightbox galeri
  document.addEventListener('click', function (ev) {
    var a = ev.target.closest && ev.target.closest('[data-lightbox]');
    if (!a) return;
    ev.preventDefault();
    var box = document.createElement('div');
    box.className = 'lightbox'; box.setAttribute('role', 'dialog'); box.setAttribute('aria-modal', 'true');
    var img = document.createElement('img'); img.src = a.getAttribute('href'); img.alt = a.getAttribute('data-caption') || '';
    var cap = document.createElement('p'); cap.textContent = a.getAttribute('data-caption') || '';
    var close = document.createElement('button'); close.className = 'btn btn--light'; close.type = 'button'; close.textContent = 'Tutup';
    box.appendChild(img); box.appendChild(cap); box.appendChild(close);
    var done = function () { document.removeEventListener('keydown', onKey); box.remove(); };
    var onKey = function (e) { if (e.key === 'Escape') done(); };
    box.addEventListener('click', function (e) { if (e.target !== img) done(); });
    document.addEventListener('keydown', onKey);
    document.body.appendChild(box); close.focus();
  });
})();
