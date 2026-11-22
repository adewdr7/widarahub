# WidaraHub — Panduan Termux & Preview Web

Platform digital desa (pilot: Desa Widarasari). Versi ini: **PHP polos + SQLite + penyimpanan file lokal**.
Belum memakai Firebase/Cloudinary/layanan cloud. Tanpa Composer, tanpa MySQL — cukup `php`.

## 1. Instalasi di Termux (sekali saja)
```bash
pkg update -y && pkg upgrade -y
pkg install -y php unzip
termux-setup-storage          # izinkan akses penyimpanan
```
Salin `widarahub.zip` ke folder Download, lalu:
```bash
cd ~
unzip ~/storage/downloads/widarahub.zip
cd widarahub
bash start.sh
```
Jika ZIP berisi folder induk lain, `cd` ke folder yang berisi `start.sh`.

## 2. Preview hasil akhir
1. Biarkan Termux tetap berjalan (`bash start.sh` aktif).
2. Buka browser HP: **http://localhost:8080**
3. Dashboard admin: **http://localhost:8080/admin**

| Akun | Username | Kata sandi | Peran |
|---|---|---|---|
| Admin | `admin` | `admin12345` | Super Admin |
| Editor uji | `editor` | `editor12345` | Editor (tidak bisa publish — uji alur persetujuan) |

**Segera ganti kata sandi** di Admin → Pengguna.

Database dan data demo dibuat otomatis saat halaman pertama dibuka (`storage/widarahub.sqlite`).
Semua konten demo bertanda **[DEMO]**; banner kuning tampil sampai Anda mencentang
"sudah diverifikasi" di Admin → Pengaturan Website.

### Alur uji cepat
1. Login `editor` → Berita → Tambah → pilih status *Ajukan* → simpan.
2. Login `admin` → Overview menampilkan "Menunggu persetujuan" → buka, ubah status ke *Terpublikasi*.
3. Buka `/informasi` — berita muncul. Edit Pengaturan Website → hero/sambutan berubah tanpa sentuh kode.

## 3. Perintah berguna
```bash
bash start.sh                      # jalankan di localhost:8080
PORT=9000 bash start.sh            # ganti port
HOST=0.0.0.0 bash start.sh         # bisa dibuka perangkat lain di Wi-Fi sama: http://IP-HP:8080
termux-wake-lock                   # cegah Termux tertidur saat preview
rm storage/widarahub.sqlite        # reset database (data demo dibuat ulang)
cp -r storage public/uploads ~/backup_widarahub/   # backup data + file unggahan
```
Berhenti: tekan `Ctrl+C` di Termux.

## 4. Struktur proyek
```
start.sh  router.php           ← menjalankan server PHP
app/                           ← logika (routes, controller, resources.php = definisi modul CMS)
database/schema.sql            ← skema tabel
views/                         ← template halaman  (UI Anda → lihat docs/PETA_UI.md)
public/                        ← document root (index.php, assets/, uploads/)
storage/                       ← database SQLite
docs/DAFTAR_GAMBAR.md          ← nama file & ukuran px semua gambar
docs/PETA_UI.md                ← letak file UI + variabel template
```

## 5. Yang sudah ada (Fase 1 MVP)
Beranda, profil desa, pemerintahan, berita (cari/kategori/pagination/terkait/bagikan), pengumuman, agenda, potensi, transparansi (dokumen), layanan, galeri + lightbox, kontak + pengaduan bertiket, pencarian, sitemap.xml, robots.txt, aksesibilitas (ukuran teks & kontras).
Dashboard: login (rate limiting, CSRF), peran & izin granular (5 peran), CMS generik semua modul, alur Draft→Diajukan→Review→Disetujui→Terpublikasi, jadwal terbit, arsip, riwayat revisi + pulihkan, media library (unggah/ganti/hapus/cari/folder), pengguna, kategori, statistik, pengaturan website, audit log, indikator kelengkapan profil.

## 6. Belum ada / catatan jujur
- Platform Management Dashboard (multi-desa), pengajuan surat online, peta interaktif: sesuai roadmap Fase 2–4.
- Editor peran/izin lewat UI belum ada (izin ada di tabel `role_permissions`).
- Backend memakai PHP polos, bukan Laravel/MySQL seperti dokumen rancangan, agar langsung jalan di Termux tanpa Composer. Struktur modular dan skema relasional sengaja dibuat mudah dipindah ke Laravel.
- Server bawaan PHP hanya untuk preview/pengembangan, bukan produksi. Untuk publik: aktifkan HTTPS, set `debug => false` di `app/config.php`.
- Sanitasi HTML memakai allowlist sederhana; untuk produksi pakai HTMLPurifier.

## 7. Masalah umum
| Gejala | Solusi |
|---|---|
| `php: command not found` | `pkg install php` |
| `could not find driver` | `pkg reinstall php` (butuh pdo_sqlite) |
| Port terpakai | `PORT=9000 bash start.sh` |
| Upload gagal | file > 5 MB atau bukan jpg/png/webp/gif/pdf/office |
| Layar error PHP | debug aktif — screenshot pesan error, kirim untuk diperbaiki |
