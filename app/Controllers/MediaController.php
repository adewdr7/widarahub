<?php
class MediaController
{
    public static function index(): void
    {
        Auth::need('media.view');
        $q = qs('q');
        $folder = qs('folder');
        $where = ['1=1'];
        $params = [];
        if ($q !== '') { $where[] = 'original_name LIKE ?'; $params[] = "%$q%"; }
        if ($folder !== '') { $where[] = 'folder = ?'; $params[] = $folder; }
        $w = implode(' AND ', $where);
        $total = (int)db_val("SELECT COUNT(*) FROM media_files WHERE $w", $params);
        $pg = paginate($total, 24, (int)qs('page') ?: 1);
        $files = db_all("SELECT * FROM media_files WHERE $w ORDER BY id DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}", $params);
        $folders = array_column(db_all('SELECT DISTINCT folder FROM media_files ORDER BY folder'), 'folder');
        view('admin/media', ['title' => 'Media', 'files' => $files, 'pg' => $pg, 'q' => $q, 'folder' => $folder, 'folders' => $folders], 'admin');
    }

    public static function upload(): void
    {
        Auth::need('media.create');
        csrf_check();
        $folder = slugify(post_str('folder', 40)) ?: 'umum';
        [$path, $err] = Uploader::handle('file', 'file', $folder);
        if ($err) flash('error', $err);
        elseif ($path === null) flash('error', 'Pilih file terlebih dahulu.');
        else { audit('upload', 'media_files', null, $path); flash('success', 'File diunggah: /' . $path); }
        redirect('/admin/media');
    }

    public static function replace(string $id): void
    {
        Auth::need('media.create');
        csrf_check();
        $m = db_one('SELECT * FROM media_files WHERE id = ?', [(int)$id]);
        if (!$m) abort(404);
        $f = $_FILES['file'] ?? null;
        if (!$f || $f['error'] !== UPLOAD_ERR_OK) { flash('error', 'Pilih file pengganti.'); redirect('/admin/media'); }
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $oldExt = strtolower(pathinfo($m['path'], PATHINFO_EXTENSION));
        $imgExt = config('upload_image_ext');
        if (!in_array($oldExt, $imgExt, true) || !in_array($ext, $imgExt, true) || @getimagesize($f['tmp_name']) === false
            || $f['size'] > (int)config('upload_max_bytes')) {
            flash('error', 'Pengganti harus berupa gambar valid dalam batas ukuran.');
            redirect('/admin/media');
        }
        $dest = BASE_PATH . '/public/' . $m['path'];
        if ($ext !== $oldExt) { flash('error', 'Ekstensi pengganti harus sama (.' . $oldExt . ').'); redirect('/admin/media'); }
        if (!move_uploaded_file($f['tmp_name'], $dest)) { flash('error', 'Gagal mengganti file.'); redirect('/admin/media'); }
        db_exec('UPDATE media_files SET size = ?, original_name = ? WHERE id = ?', [(int)$f['size'], mb_substr($f['name'], 0, 200), (int)$id]);
        audit('replace', 'media_files', (int)$id, $m['path']);
        flash('success', 'Gambar diganti.');
        redirect('/admin/media');
    }

    public static function destroy(string $id): void
    {
        Auth::need('media.delete');
        csrf_check();
        $m = db_one('SELECT * FROM media_files WHERE id = ?', [(int)$id]);
        if (!$m) abort(404);
        $file = BASE_PATH . '/public/' . $m['path'];
        if (strpos($m['path'], 'uploads/') === 0 && is_file($file)) @unlink($file);
        db_exec('DELETE FROM media_files WHERE id = ?', [(int)$id]);
        audit('delete', 'media_files', (int)$id, $m['path']);
        flash('success', 'File dihapus. Konten yang memakai file ini akan menampilkan placeholder.');
        redirect('/admin/media');
    }
}
