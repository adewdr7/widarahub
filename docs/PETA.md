# Peta OpenStreetMap

- **/kontak** menampilkan peta kantor desa + tombol "Buka rute" (Google Maps) dan "Lihat di OpenStreetMap".
- **/potensi** menampilkan peta semua potensi yang punya koordinat (ikut filter kategori) + penanda kantor desa.
- **/potensi/{slug}** menampilkan peta lokasi potensi tersebut.

## Mengisi koordinat
- Kantor desa: Admin → Pengaturan Website → *Kontak & Media Sosial* → Latitude/Longitude. Nilai awal sekitar -6.94936, 108.52098 (dari tautan Google Maps Kantor Kepala Desa Widarasari). Cek posisinya di peta, geser bila perlu.
- Potensi desa: Admin → Potensi Desa → kolom Latitude/Longitude.
- Cara mengambil koordinat: di Google Maps tekan lama pada titik, angka koordinat muncul di atas/bawah; atau di openstreetmap.org klik kanan → "Show address". Pakai format desimal (titik atau koma), mis. -6.94936.

## Catatan
- Peta memuat Leaflet (cdnjs) dan ubin dari tile.openstreetmap.org, jadi **perlu internet** saat dilihat. Tanpa internet, halaman tetap jalan dan peta diganti pesan singkat.
- Penggunaan ubin OSM harus mengikuti kebijakan mereka (cukup untuk lalu lintas ringan). Untuk lalu lintas besar, gunakan penyedia ubin sendiri.
- Kantor desa dan potensi tanpa koordinat tidak tampil di peta.
