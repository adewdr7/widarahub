#!/data/data/com.termux/files/usr/bin/bash
# Menjalankan WidaraHub di http://localhost:8080
cd "$(dirname "$0")" || exit 1
PORT="${PORT:-8080}"
HOST="${HOST:-127.0.0.1}"   # ganti ke 0.0.0.0 agar bisa dibuka dari perangkat lain di Wi-Fi yang sama
if ! command -v php >/dev/null 2>&1; then
  echo "PHP belum terpasang. Jalankan: pkg install php"; exit 1
fi
mkdir -p storage public/uploads
echo "WidaraHub berjalan di: http://localhost:${PORT}"
echo "Admin: http://localhost:${PORT}/admin   (Ctrl+C untuk berhenti)"
exec php -d upload_max_filesize=6M -d post_max_size=8M -d memory_limit=128M \
  -S "${HOST}:${PORT}" -t public router.php
