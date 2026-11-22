# Peran & Izin

Admin → **Peran & Izin** (hanya Super Admin secara bawaan; bisa diberikan ke peran lain lewat izin `roles.*`).

- 5 peran bawaan tidak dapat dihapus; peran baru bisa dibuat (mis. "Operator Keuangan").
- Matriks izin per modul: Lihat · Tambah · Ubah · Hapus · Publikasikan. Perubahan langsung berlaku (tanpa login ulang).
- Pengaman: Super Admin tidak dapat diubah; pengguna non-Super tidak dapat mengubah perannya sendiri, tidak dapat memberi izin yang tidak ia miliki, dan tidak dapat memberi pengguna peran yang izinnya melebihi miliknya; peran yang masih dipakai tidak dapat dihapus.
- Semua perubahan tercatat di Audit Log (izin yang ditambah/dicabut).
- Data sensitif (Minat Warga, Pengguna, Audit Log) sebaiknya hanya diberikan ke orang yang memang berwenang.
