# Modul Keuangan & Tujuan Desa

Halaman publik: **/keuangan** (menu "Keuangan"). Kelola di Admin → *Keuangan Desa*, *Tujuan & Sasaran*, *Kebutuhan & Peluang*.

| Bagian | Fungsi |
|---|---|
| Keuangan Desa | Anggaran & realisasi per tahun (pendapatan/belanja/pembiayaan), dikelompokkan per bidang. Ringkasan + persen realisasi otomatis. |
| Tujuan & Sasaran | 5 pilar: Ekonomi, SDM, Pendidikan, Infrastruktur, Keragaman Layanan. Isi kondisi awal, capaian, target → progres dihitung otomatis. |
| Kebutuhan & Peluang | Daftar layanan/usaha/fasilitas yang belum ada, sedang dirintis, atau sudah tersedia, supaya warga tahu peluang merintis usaha dan uang tidak terus keluar desa. |
| Usulan warga | Form di /kontak punya jenis baru "Usulan layanan / usaha desa"; masuk ke Admin → Pengaduan, bisa dipertimbangkan menjadi item Kebutuhan & Peluang. |

Semua modul memakai alur persetujuan yang sama (Draft → … → Terpublikasi). Data demo bertanda [DEMO] dan angkanya bukan data resmi.
Tabel dibuat otomatis saat halaman pertama dibuka (database lama tidak dihapus). Izin baru: `budgets.*`, `goals.*`, `needs.*`.

## Tambahan: Pelatihan SDM, Minat Warga, UMKM
- **/pelatihan** — jadwal pelatihan, kuota, tombol daftar. Kelola di Admin → *Pelatihan SDM* (bisa dikaitkan ke sasaran pilar SDM).
- **Minat warga** — warga bisa menyatakan minat pada item *Kebutuhan & Peluang* (di /keuangan) dan mendaftar pelatihan. Publik hanya melihat **jumlah**; nama & kontak hanya di Admin → *Minat Warga* (Super Admin & Admin Desa). Ada persetujuan wajib, anti-spam, dan anti-duplikat. Hapus data yang tidak diperlukan lagi.
- **UMKM** — Potensi Desa kini punya kolom produk/jasa, kisaran harga, dan WhatsApp. WhatsApp hanya tampil bila kolom "Pemilik setuju" dicentang. Filter UMKM: `/potensi?kategori=umkm`.
- Link /pelatihan tersedia dari halaman /keuangan dan sitemap; tambahkan ke menu bila perlu di `config('nav')`.
