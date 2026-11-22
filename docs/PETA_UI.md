# Peta File UI — di mana menaruh UI buatan Anda

## 1. Gaya (CSS)
| Path | Fungsi |
|---|---|
| `public/assets/css/theme.css` | **Taruh gaya Anda di sini.** Dimuat setelah `base.css`, jadi menimpa. Paling aman. |
| `public/assets/css/base.css` | Kerangka bawaan + token warna (`--primary`, dst.). Boleh diganti total. |
| `public/assets/css/admin.css` | Gaya dashboard admin (terpisah dari situs publik). |
| `public/assets/js/app.js` | JS publik (menu mobile, ukuran teks/kontras, lightbox). |
| `public/assets/js/admin.js` | JS admin (sidebar, konfirmasi hapus, editor teks). |

## 2. Template halaman publik (PHP) — `views/`
Ganti isinya dengan HTML buatan Anda; **cukup pertahankan variabel yang dipakai** (lihat kolom Variabel).
Keluarkan teks dengan `<?= e($variabel) ?>` agar aman dari XSS.

| File | URL | Variabel tersedia |
|---|---|---|
| `layouts/public.php` | semua halaman publik | `$title`, `$content`, `$v` (desa), helper `setting('kunci')`, `config('nav')` |
| `public/home.php` | `/` | `$posts`, `$potentials`, `$announcements`, `$events`, `$stats`, `$quick` |
| `public/profile.php` | `/profil` | `$pages` |
| `public/page.php` | `/profil/{slug}`, `/halaman/{slug}` | `$page`, `$siblings` |
| `public/government.php` | `/pemerintahan` | `$groups` (`label`, `members[]`) |
| `public/posts.php` | `/informasi` | `$posts`, `$pg`, `$q`, `$cat`, `$categories` |
| `public/post.php` | `/informasi/{slug}` | `$post`, `$related` |
| `public/announcements.php` | `/pengumuman` | `$items`, `$pg` |
| `public/events.php` | `/agenda` | `$upcoming`, `$past` |
| `public/potentials.php` | `/potensi` | `$items`, `$pg`, `$cat`, `$categories` |
| `public/potential.php` | `/potensi/{slug}` | `$item` |
| `public/documents.php` | `/transparansi` | `$items`, `$pg`, `$cat`, `$categories` |
| `public/services.php` | `/layanan` | `$items` |
| `public/gallery.php` | `/galeri` | `$items`, `$pg`, `$cat`, `$categories` |
| `public/contact.php` | `/kontak` | `$ticket`, `$lookup` |
| `public/search.php` | `/cari` | `$q`, `$results` |
| `errors/error.php` | 403/404/419 | `$code`, `$title`, `$message` |

Potongan yang dipakai ulang (`views/partials/`): `post_card.php`, `filter_bar.php`, `pagination.php`. Panggil dengan `partial('nama', [...])`.

## 3. Template admin — `views/admin/` dan `views/layouts/admin.php`, `auth.php`
Form dibuat otomatis dari definisi di `app/resources.php` (tidak perlu HTML per modul). Ubah tampilan lewat `admin.css` atau `views/admin/_fields.php`.

## 4. Helper berguna di template
`e($teks)` · `media_url($path, $fallback)` · `id_date($tanggal, true)` · `excerpt($html, 120)` · `clean_html($html)` · `setting('hero_title')` · `village()` (data desa) · `csrf_field()` · `Auth::can('posts.edit')`.

## 5. Token warna (dari dokumen desain)
Primary `#176B4A` · Primary Dark `#123D32` · Primary Light `#E8F4ED` · Accent `#D6A84B` · Background `#F8FAF8` · Surface `#FFFFFF` · Text `#1D2923` · Text 2 `#68766E` · Border `#E3EAE5` · Danger `#D64545`.

## 6. Menambah modul/field baru
Tambahkan blok di `app/resources.php` + tabel di `database/schema.sql` + permission di `app/Installer.php`. (Untuk database yang sudah ada, hapus `storage/widarahub.sqlite` agar dibuat ulang — data demo akan hilang.)
