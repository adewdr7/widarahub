// WidaraHub — peta OpenStreetMap dengan Leaflet. Data penanda dibaca dari atribut data-markers.
(function () {
  document.querySelectorAll('[data-map]').forEach(function (el) {
    if (!window.L) { el.textContent = 'Peta memerlukan koneksi internet.'; return; }
    var data;
    try { data = JSON.parse(el.getAttribute('data-markers') || '[]'); } catch (e) { data = []; }
    if (!data.length) return;

    var map = L.map(el, { scrollWheelZoom: false });
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19, attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var bounds = [];
    data.forEach(function (p) {
      var ll = [p.lat, p.lng];
      bounds.push(ll);
      var node = document.createElement('div');
      var strong = document.createElement('strong');
      strong.textContent = p.title || '';
      node.appendChild(strong);
      if (p.url && p.url.charAt(0) === '/') {
        node.appendChild(document.createElement('br'));
        var a = document.createElement('a');
        a.href = p.url; a.textContent = 'Lihat detail';
        node.appendChild(a);
      }
      L.marker(ll).addTo(map).bindPopup(node);
    });

    if (data.length === 1) map.setView(bounds[0], 16);
    else map.fitBounds(bounds, { padding: [30, 30], maxZoom: 16 });
    // aktifkan zoom scroll hanya setelah peta diklik, agar tidak mengganggu scroll halaman
    map.once('focus', function () { map.scrollWheelZoom.enable(); });
  });
})();
