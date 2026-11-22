<?php
/** CRUD generik untuk semua modul di app/resources.php */
class ResourceController
{
    private static function res(string $key): array
    {
        $all = require BASE_PATH . '/app/resources.php';
        if (!isset($all[$key])) abort(404);
        $r = $all[$key];
        $r['key'] = $key;
        return $r;
    }

    private static function find(array $res, int $id): array
    {
        $item = db_one("SELECT * FROM {$res['table']} WHERE id = ? AND deleted_at IS NULL", [$id]);
        if (!$item) abort(404);
        return $item;
    }

    private static function guard(array $res, array $item): void
    {
        if ($res['key'] === 'users' && !Auth::isSuper()) {
            $role = db_val('SELECT slug FROM roles WHERE id = ?', [$item['role_id']]);
            if ($role === 'super_admin') abort(403, 'Hanya Super Admin yang dapat mengubah akun Super Admin.');
        }
    }

    /** Field form + field alur persetujuan (draft → publikasi) */
    public static function fieldsFor(array $res): array
    {
        $f = $res['fields'];
        if (!empty($res['workflow'])) {
            $canPub = Auth::can($res['perm'] . '.publish');
            $opts = $canPub
                ? ['draft' => 'Draft', 'submitted' => 'Diajukan', 'review' => 'Dalam Review', 'approved' => 'Disetujui',
                   'published' => 'Terpublikasi', 'rejected' => 'Ditolak (kembali ke editor)', 'archived' => 'Arsip']
                : ['draft' => 'Draft', 'submitted' => 'Ajukan untuk persetujuan'];
            $f[] = ['name' => 'published_at', 'label' => 'Jadwal terbit (kosongkan = langsung saat dipublikasikan)', 'type' => 'datetime'];
            $f[] = ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'options' => $opts,
                    'hint' => $canPub ? '' : 'Konten terpublikasi yang Anda ubah akan kembali menunggu persetujuan.'];
            if ($canPub) $f[] = ['name' => 'review_note', 'label' => 'Catatan revisi (terlihat oleh editor)', 'type' => 'textarea'];
        }
        return $f;
    }

    public static function relationOptions(array $f): array
    {
        $cond = [];
        $p = [];
        foreach (($f['where'] ?? []) as $k => $v) { $cond[] = "$k = ?"; $p[] = $v; }
        if (!empty($f['soft'])) $cond[] = 'deleted_at IS NULL';
        if ($f['table'] === 'roles' && !Auth::isSuper()) $cond[] = "slug != 'super_admin'";
        $w = $cond ? ' WHERE ' . implode(' AND ', $cond) : '';
        $o = [];
        foreach (db_all("SELECT id, {$f['label_col']} AS label FROM {$f['table']}{$w} ORDER BY label", $p) as $r) $o[$r['id']] = $r['label'];
        return $o;
    }

    private static function relationMaps(array $res): array
    {
        $maps = [];
        foreach ($res['fields'] as $f) {
            if ($f['type'] === 'relation') {
                $g = $f; unset($g['where'], $g['soft']);
                $maps[$f['name']] = self::relationOptions($g);
            }
        }
        return $maps;
    }

    private static function cell(array $res, string $col, array $row, array $maps): string
    {
        $v = $row[$col] ?? '';
        foreach ($res['fields'] as $f) {
            if (($f['column'] ?? $f['name']) !== $col) continue;
            switch ($f['type']) {
                case 'relation': return (string)($maps[$f['name']][$v] ?? '-');
                case 'select': return (string)(($f['options'][$v] ?? $v));
                case 'checkbox': return $v ? 'Ya' : 'Tidak';
                case 'datetime': return id_date((string)$v, true);
                case 'date': return id_date((string)$v);
            }
        }
        if ($col === 'status') return status_label((string)$v);
        if (in_array($col, ['published_at', 'updated_at', 'created_at', 'starts_at', 'last_login_at'], true)) return id_date((string)$v, true) ?: '-';
        $s = excerpt((string)$v, 80);
        return $s !== '' ? $s : '-';
    }

    // ---------------- Aksi ----------------

    public static function index(string $key): void
    {
        $res = self::res($key);
        Auth::need($res['perm'] . '.view');

        $q = qs('q');
        $status = qs('status');
        $where = ['deleted_at IS NULL'];
        $params = [];
        if ($q !== '') { $where[] = "{$res['title']} LIKE ?"; $params[] = "%$q%"; }
        if ($status !== '' && !empty($res['workflow'])) { $where[] = 'status = ?'; $params[] = $status; }
        $w = implode(' AND ', $where);

        $total = (int)db_val("SELECT COUNT(*) FROM {$res['table']} WHERE $w", $params);
        $pg = paginate($total, 15, (int)qs('page') ?: 1);
        $order = $res['order'] ?? 'id DESC';
        $items = db_all("SELECT * FROM {$res['table']} WHERE $w ORDER BY $order LIMIT {$pg['per']} OFFSET {$pg['offset']}", $params);

        $maps = self::relationMaps($res);
        $rows = [];
        foreach ($items as $it) {
            $cells = [];
            foreach ($res['list'] as $col => $lab) $cells[$col] = self::cell($res, $col, $it, $maps);
            $rows[] = ['id' => $it['id'], 'status' => $it['status'] ?? null, 'note' => $it['review_note'] ?? '', 'cells' => $cells];
        }
        view('admin/list', ['title' => $res['label'], 'res' => $res, 'rows' => $rows, 'pg' => $pg, 'q' => $q, 'status' => $status], 'admin');
    }

    public static function create(string $key): void
    {
        $res = self::res($key);
        Auth::need($res['perm'] . '.create');
        if (!empty($res['no_create'])) abort(404);
        $values = [];
        foreach ($res['fields'] as $f) if (isset($f['default'])) $values[$f['column'] ?? $f['name']] = $f['default'];
        if (!empty($res['workflow'])) $values['status'] = 'draft';
        self::form($res, null, $values, []);
    }

    public static function store(string $key): void
    {
        $res = self::res($key);
        Auth::need($res['perm'] . '.create');
        csrf_check();
        if (!empty($res['no_create'])) abort(404);

        [$data, $errors] = self::collect($res, null);
        if ($errors) { self::form($res, null, array_merge($data, $_POST), $errors); return; }

        $data['created_at'] = now();
        $data['updated_at'] = now();
        if (!empty($res['workflow'])) $data['created_by'] = Auth::user()['id'];
        $id = db_insert($res['table'], $data);
        audit('create', $res['table'], $id, (string)($data[$res['title']] ?? ''));
        flash('success', 'Data berhasil ditambahkan.');
        redirect('/admin/r/' . $key);
    }

    public static function edit(string $key, string $id): void
    {
        $res = self::res($key);
        Auth::need($res['perm'] . '.edit');
        $item = self::find($res, (int)$id);
        self::guard($res, $item);
        self::form($res, $item, $item, []);
    }

    public static function update(string $key, string $id): void
    {
        $res = self::res($key);
        Auth::need($res['perm'] . '.edit');
        csrf_check();
        $item = self::find($res, (int)$id);
        self::guard($res, $item);

        [$data, $errors] = self::collect($res, $item);
        if ($errors) { self::form($res, $item, array_merge($item, $_POST), $errors); return; }

        self::snapshot($res, $item);
        $data['updated_at'] = now();
        db_update($res['table'], (int)$id, $data);
        audit('update', $res['table'], (int)$id, (string)($data[$res['title']] ?? $item[$res['title']] ?? ''));
        flash('success', 'Perubahan disimpan.');
        redirect('/admin/r/' . $key);
    }

    public static function destroy(string $key, string $id): void
    {
        $res = self::res($key);
        Auth::need($res['perm'] . '.delete');
        csrf_check();
        $item = self::find($res, (int)$id);
        self::guard($res, $item);
        if (!empty($res['protect_self']) && (int)$id === (int)Auth::user()['id']) {
            flash('error', 'Anda tidak dapat menghapus akun yang sedang dipakai.');
            redirect('/admin/r/' . $key);
        }
        db_update($res['table'], (int)$id, ['deleted_at' => now()]);   // soft delete
        audit('delete', $res['table'], (int)$id, (string)($item[$res['title']] ?? ''));
        flash('success', 'Data dihapus.');
        redirect('/admin/r/' . $key);
    }

    public static function restore(string $key, string $id, string $rev): void
    {
        $res = self::res($key);
        Auth::need($res['perm'] . '.edit');
        csrf_check();
        $item = self::find($res, (int)$id);
        $row = db_one('SELECT * FROM revisions WHERE id = ? AND table_name = ? AND record_id = ?', [(int)$rev, $res['table'], (int)$id]);
        if (!$row) abort(404);

        $snap = json_decode($row['snapshot'], true) ?: [];
        $allowed = [];
        foreach ($res['fields'] as $f) {
            if (in_array($f['type'], ['password', 'readonly', 'slug'], true)) continue;
            $c = $f['column'] ?? $f['name'];
            if (array_key_exists($c, $snap)) $allowed[$c] = $snap[$c];
        }
        if (!empty($res['workflow'])) $allowed['status'] = 'draft';
        self::snapshot($res, $item);
        $allowed['updated_at'] = now();
        db_update($res['table'], (int)$id, $allowed);
        audit('restore', $res['table'], (int)$id, 'revisi #' . (int)$rev);
        flash('success', 'Revisi dipulihkan sebagai draft.');
        redirect("/admin/r/$key/$id/edit");
    }

    // ---------------- Internal ----------------

    private static function snapshot(array $res, array $item): void
    {
        if (empty($res['revisions'])) return;
        db_insert('revisions', [
            'table_name' => $res['table'], 'record_id' => (int)$item['id'],
            'snapshot' => json_encode($item, JSON_UNESCAPED_UNICODE), 'user_id' => Auth::user()['id'], 'created_at' => now(),
        ]);
    }

    private static function form(array $res, ?array $item, array $values, array $errors): void
    {
        $revisions = [];
        if ($item && !empty($res['revisions'])) {
            $revisions = db_all('SELECT r.id, r.created_at, u.username FROM revisions r LEFT JOIN users u ON u.id = r.user_id
                                 WHERE r.table_name = ? AND r.record_id = ? ORDER BY r.id DESC LIMIT 10', [$res['table'], $item['id']]);
        }
        view('admin/form', [
            'title' => ($item ? 'Edit ' : 'Tambah ') . $res['label'], 'res' => $res, 'item' => $item,
            'values' => $values, 'errors' => $errors, 'fields' => self::fieldsFor($res), 'revisions' => $revisions,
        ], 'admin');
    }

    private static function collect(array $res, ?array $existing): array
    {
        $data = [];
        $errors = [];

        foreach (self::fieldsFor($res) as $f) {
            $n = $f['name'];
            $type = $f['type'];
            $col = $f['column'] ?? $n;
            if ($type === 'readonly') continue;

            $raw = $_POST[$n] ?? '';
            $raw = is_string($raw) ? $raw : '';
            $val = trim($raw);

            switch ($type) {
                case 'checkbox':
                    $data[$col] = isset($_POST[$n]) ? 1 : 0;
                    continue 2;

                case 'password':
                    if ($val === '') {
                        if (!$existing && !empty($f['required'])) $errors[$n] = 'Kata sandi wajib diisi.';
                        continue 2;
                    }
                    if (strlen($val) < 8) { $errors[$n] = 'Kata sandi minimal 8 karakter.'; continue 2; }
                    $data[$col] = password_hash($val, PASSWORD_DEFAULT);
                    continue 2;

                case 'image':
                case 'file':
                    [$path, $err] = Uploader::handle($n, $type, $f['folder'] ?? 'umum');
                    if ($err) $errors[$n] = $err;
                    $data[$col] = $path ?? ($existing[$col] ?? '');
                    break;

                case 'number':
                    $data[$col] = $val === '' ? 0 : (int)$val;
                    break;

                case 'datetime':
                    $data[$col] = $val === '' ? null : date('Y-m-d H:i:s', strtotime($val) ?: time());
                    break;

                case 'date':
                    $data[$col] = $val === '' ? null : date('Y-m-d', strtotime($val) ?: time());
                    break;

                case 'richtext':
                    $data[$col] = clean_html($raw);
                    break;

                case 'slug':
                    $base = $val !== '' ? slugify($val) : slugify((string)($data[$f['from']] ?? ''));
                    $slug = $base;
                    $i = 2;
                    while (true) {
                        $sql = "SELECT COUNT(*) FROM {$res['table']} WHERE slug = ? AND id != ?";
                        $p = [$slug, $existing['id'] ?? 0];
                        if (!empty($f['scope'])) { $sql .= " AND {$f['scope']} = ?"; $p[] = $data[$f['scope']] ?? ''; }
                        if (!(int)db_val($sql, $p)) break;
                        $slug = $base . '-' . $i++;
                    }
                    $data[$col] = $slug;
                    break;

                case 'select':
                    if (!array_key_exists($val, $f['options'])) $errors[$n] = 'Pilihan tidak valid.';
                    $data[$col] = $val;
                    break;

                case 'relation':
                    $opts = self::relationOptions($f);
                    if ($val !== '' && !isset($opts[$val])) $errors[$n] = 'Pilihan tidak valid.';
                    $data[$col] = $val === '' ? null : (int)$val;
                    break;

                case 'coord':
                    $val = str_replace(',', '.', $val);
                    if ($val !== '' && (!is_numeric($val) || abs((float)$val) > $f['range'])) $errors[$n] = 'Koordinat tidak valid.';
                    $data[$col] = ($val === '' || !is_numeric($val)) ? null : (float)$val;
                    break;

                default:
                    $data[$col] = $val;
            }

            if (!empty($f['required']) && $type !== 'slug') {
                $v = $data[$col] ?? '';
                if ($v === '' || $v === null) $errors[$n] = $errors[$n] ?? ($f['label'] . ' wajib diisi.');
            }
            if (!empty($f['unique']) && ($data[$col] ?? '') !== '' && empty($errors[$n])) {
                $c = (int)db_val("SELECT COUNT(*) FROM {$res['table']} WHERE {$col} = ? AND id != ?", [$data[$col], $existing['id'] ?? 0]);
                if ($c) $errors[$n] = $f['label'] . ' sudah dipakai.';
            }
        }

        if (!empty($res['workflow'])) {
            $canPub = Auth::can($res['perm'] . '.publish');
            $status = $data['status'] ?? 'draft';
            if (!$canPub && !in_array($status, ['draft', 'submitted'], true)) $status = 'draft';
            $data['status'] = $status;
            if ($status === 'published' && empty($data['published_at'])) $data['published_at'] = now();
        }
        return [$data, $errors];
    }
}
