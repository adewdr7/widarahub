// WidaraHub — JS dashboard admin (sidebar, konfirmasi hapus, editor teks sederhana)
(function () {
  var side = document.getElementById('adminSide'), t = document.getElementById('sideToggle');
  if (side && t) t.addEventListener('click', function () { side.classList.toggle('is-open'); });

  document.addEventListener('submit', function (e) {
    var msg = e.target.getAttribute && e.target.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) e.preventDefault();
  });

  // Rich text editor sederhana (progressive enhancement: tanpa JS tetap berupa textarea)
  document.querySelectorAll('[data-rte]').forEach(function (box) {
    var bar = box.querySelector('.rte__bar'), area = box.querySelector('.rte__area'), ta = box.querySelector('textarea');
    if (!bar || !area || !ta) return;
    bar.hidden = false; area.hidden = false; ta.hidden = true;
    var sync = function () { ta.value = area.innerHTML; };
    area.addEventListener('input', sync);
    bar.addEventListener('click', function (e) {
      var b = e.target.closest('button[data-cmd]'); if (!b) return;
      var cmd = b.getAttribute('data-cmd'), arg = null;
      if (cmd.indexOf(':') > -1) { var p = cmd.split(':'); cmd = p[0]; arg = p[1]; }
      if (cmd === 'createLink') { arg = window.prompt('Alamat tautan (https://...)'); if (!arg) return; }
      area.focus(); document.execCommand(cmd, false, arg); sync();
    });
    var form = box.closest('form'); if (form) form.addEventListener('submit', sync);
  });
})();
