<?php
/** Upload ke penyimpanan LOKAL (public/uploads). Belum memakai layanan cloud. */
class Uploader
{
    /** @return array [path|null, error|null] */
    public static function handle(string $field, string $type = 'image', string $folder = 'umum'): array
    {
        if (empty($_FILES[$field]) || !is_array($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }
        $f = $_FILES[$field];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            return [null, 'Unggahan gagal (kode ' . $f['error'] . '). Periksa batas ukuran file.'];
        }
        return self::save($f, $type, $folder);
    }

    public static function save(array $f, string $type = 'image', string $folder = 'umum'): array
    {
        $max = (int)config('upload_max_bytes');
        if ($f['size'] > $max) return [null, 'Ukuran file melebihi ' . round($max / 1048576) . ' MB.'];

        $imgExt = config('upload_image_ext');
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $allowed = $type === 'image' ? $imgExt : array_merge($imgExt, config('upload_doc_ext'));
        if (!in_array($ext, $allowed, true)) return [null, 'Tipe file .' . $ext . ' tidak diizinkan.'];
        if (in_array($ext, $imgExt, true) && @getimagesize($f['tmp_name']) === false) {
            return [null, 'File bukan gambar yang valid.'];
        }

        $sub = 'uploads/' . date('Y') . '/' . date('m');
        $dir = BASE_PATH . '/public/' . $sub;
        if (!is_dir($dir) && !mkdir($dir, 0775, true)) return [null, 'Folder upload tidak dapat dibuat.'];

        $name = bin2hex(random_bytes(8)) . '.' . $ext;
        if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $name)) return [null, 'Gagal menyimpan file.'];

        $path = $sub . '/' . $name;
        $u = Auth::user();
        db_insert('media_files', [
            'original_name' => mb_substr($f['name'], 0, 200),
            'path' => $path,
            'mime' => (string)($f['type'] ?? ''),
            'size' => (int)$f['size'],
            'folder' => $folder,
            'uploaded_by' => $u['id'] ?? null,
            'created_at' => now(),
        ]);
        return [$path, null];
    }
}
