# Daftar Gambar WidaraHub

Semua path relatif terhadap folder proyek. Taruh file persis dengan **nama** dan **ukuran** di bawah.
Jika file belum ada, situs otomatis memakai placeholder (`public/assets/img/placeholder.svg` / `placeholder-orang.svg`), jadi web tetap jalan.

Aturan umum: JPG/WebP untuk foto (kualitas 75–82), PNG untuk gambar transparan/bagan, SVG untuk logo & ikon. Usahakan tiap foto < 300 KB.

## A. Brand — `public/assets/img/brand/`
| File | Ukuran (px) | Format | Dipakai di | Catatan |
|---|---|---|---|---|
| `logo-widarahub-putih.svg` | 320 × 80 | SVG | Header & footer (latar hijau tua) | Versi putih. Sudah ada placeholder |
| `logo-widarahub.svg` | 320 × 80 | SVG | Halaman login admin | Versi berwarna. Sudah ada placeholder |
| `logo-icon.png` | 512 × 512 | PNG transparan | Cadangan logo bila SVG tak ada | Ikon persegi |
| `logo-desa.png` | 512 × 512 | PNG transparan | Opsional (lambang desa) | Belum dipakai di template bawaan |
| `favicon-32.png` | 32 × 32 | PNG | Tab browser | |
| `favicon-180.png` | 180 × 180 | PNG | Ikon layar utama iOS | |
| `og-default.jpg` | 1200 × 630 | JPG | Pratinjau saat tautan dibagikan (WhatsApp/Facebook) | |

## B. Hero & Halaman — `public/assets/img/hero/`, `pages/`, `auth/`
| File | Ukuran (px) | Format | Dipakai di | Catatan |
|---|---|---|---|---|
| `hero/hero-desa.jpg` | 1920 × 1080 | JPG/WebP | Hero beranda | Rasio 16:9; subjek di tengah (mobile dipotong `cover`). Bisa diganti dari Admin → Pengaturan Website |
| `pages/struktur-organisasi.png` | 1600 × 1000 | PNG/JPG | Halaman Pemerintahan | Bagan struktur. Bisa diganti dari Admin → Pengaturan |
| `auth/login-bg.jpg` | 1200 × 1600 | JPG | Latar halaman login admin | Opsional |
| *(upload admin)* banner halaman | 1600 × 400 | JPG/WebP | Atas halaman profil | Diunggah per halaman di Admin → Profil & Halaman |

## C. Orang — `public/assets/img/people/`
| File | Ukuran (px) | Format | Dipakai di | Catatan |
|---|---|---|---|---|
| `kepala-desa.jpg` | 600 × 750 | JPG | Sambutan Kepala Desa (beranda) | Rasio 4:5 |
| `pejabat-01.jpg` … `pejabat-05.jpg` | 400 × 500 | JPG | Halaman Pemerintahan (data demo) | Rasio 4:5. Data asli diunggah lewat Admin → Pemerintahan |

## D. Ikon Akses Cepat — `public/assets/img/icons/` (SVG, 64 × 64)
`ikon-profil.svg`, `ikon-berita.svg`, `ikon-pelayanan.svg`, `ikon-potensi.svg`, `ikon-pengumuman.svg`, `ikon-kontak.svg`
Gunakan gaya ikon yang konsisten (garis/isi, warna `#176B4A`). Sudah ada placeholder lingkaran.

## E. Konten Demo
| File | Ukuran (px) | Dipakai di |
|---|---|---|
| `news/berita-01.jpg`, `berita-02.jpg`, `berita-03.jpg` | 1200 × 675 (16:9) | Thumbnail berita demo |
| `potential/potensi-01.jpg`, `potensi-02.jpg`, `potensi-03.jpg` | 1200 × 800 (3:2) | Foto potensi demo |
| `gallery/galeri-01.jpg` … `galeri-06.jpg` | 1600 × 1067 (3:2) | Galeri demo |

## F. Gambar yang diunggah lewat Dashboard (bukan file tetap)
Disimpan otomatis di `public/uploads/TAHUN/BULAN/`. Ukuran yang disarankan tampil di setiap form:
| Modul | Ukuran (px) |
|---|---|
| Berita — thumbnail | 1200 × 675 |
| Potensi — foto utama | 1200 × 800 |
| Pemerintahan — foto | 400 × 500 |
| Galeri — foto/sampul video | 1600 × 1067 |
| Halaman — banner | 1600 × 400 |
| Pengaturan — hero / Kepala Desa / bagan | 1920 × 1080 / 600 × 750 / 1600 × 1000 |
Batas ukuran file: 5 MB (ubah di `app/config.php` → `upload_max_bytes`).

## G. Font (opsional) — `public/assets/fonts/`
| File | Keterangan |
|---|---|
| `PlusJakartaSans-Variable.woff2` | Font judul |
| `Inter-Variable.woff2` | Font isi |
Tanpa file ini, situs memakai font sistem (tetap terbaca baik).

## H. Target viewport desain
360 px (mobile) · 768 px (tablet) · 1024 px (laptop) · 1440 px (desktop).
