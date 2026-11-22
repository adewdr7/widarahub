<?php
/** Editor peran & izin (RBAC granular). Pengaman: tidak bisa memberi izin yang tidak dimiliki, tidak bisa mengubah peran sendiri/Super Admin. */
class RoleController
{
    private const SYSTEM = ['super_admin', 'admin_desa', 'editor', 'operator', 'viewer'];
    private const ACTIONS = ['view' => 'Lihat', 'create' => 'Tambah', 'edit' => 'Ubah', 'delete' => 'Hapus', 'publish' => 'Publikasikan'];

    private static function catalog(): array
    {
        $labels = [];
        foreach (require BASE_PATH . '/app/resources.php' as $r) $labels[$r['perm']] = $r['label'];
        $labels += ['media' => 'Media', 'settings' => 'Pengaturan Website', 'audit' => 'Audit Log', 'roles' => 'Peran & Izin'];
        $cat = [];
        foreach (db_all('SELECT slug FROM permissions ORDER BY id') as $r) {
            [$m, $a] = explode('.', $r['slug'], 2);
            $cat[$m][$a] = $r['slug'];
        }
        $out = [];
        foreach ($cat as $m => $acts) $out[$m] = ['label' => $labels[$m] ?? $m, 'actions' => $acts];
        return $out;
    }

    private static function find(int $id): array
    {
        $role = db_one('SELECT * FROM roles WHERE id = ?', [$id]);
        if (!$role) abort(404);
        return $role;
    }

    private static function guard(array $role): void
    {
        if ($role['slug'] === 'super_admin') abort(403, 'Peran Super Admin tidak dapat diubah agar sistem tidak terkunci.');
        $u = Auth::user();
        if (!Auth::isSuper() && (int)$u['role_id'] === (int)$role['id']) abort(403, 'Anda tidak dapat mengubah peran Anda sendiri.');
    }

    public static function index(): void
    {
        Auth::need('roles.view');
        $roles = db_all('SELECT r.*, (SELECT COUNT(*) FROM users u WHERE u.role_id = r.id AND u.deleted_at IS NULL) AS user_count,
                                (SELECT COUNT(*) FROM role_permissions rp WHERE rp.role_id = r.id) AS perm_count FROM roles r ORDER BY r.id');
        view('admin/roles', ['title' => 'Peran & Izin', 'roles' => $roles, 'system' => self::SYSTEM, 'total' => (int)db_val('SELECT COUNT(*) FROM permissions')], 'admin');
    }

    private static function form(?array $role, array $selected, array $errors, array $values = []): void
    {
        view('admin/role_form', [
            'title' => $role ? 'Peran: ' . $role['name'] : 'Peran Baru', 'role' => $role, 'catalog' => self::catalog(),
            'actions' => self::ACTIONS, 'selected' => $selected, 'errors' => $errors, 'values' => $values,
            'mine' => Auth::perms(), 'isSuper' => Auth::isSuper(), 'isSystem' => $role ? in_array($role['slug'], self::SYSTEM, true) : false,
            'locked' => $role && $role['slug'] === 'super_admin',
        ], 'admin');
    }

    public static function create(): void
    {
        Auth::need('roles.create');
        self::form(null, [], [], ['name' => '', 'description' => '']);
    }

    public static function edit(string $id): void
    {
        Auth::need('roles.view');
        $role = self::find((int)$id);
        if ($role['slug'] !== 'super_admin' && !Auth::can('roles.edit')) abort(403);
        $u = Auth::user();
        if (!Auth::isSuper() && (int)$u['role_id'] === (int)$role['id']) abort(403, 'Anda tidak dapat mengubah peran Anda sendiri.');
        self::form($role, Auth::permsOfRole((int)$role['id']), [], $role);
    }

    /** Izin akhir: yang boleh diatur pengguna dari input, ditambah izin lama yang tidak boleh diubahnya. */
    private static function finalPerms(array $existing): array
    {
        $posted = $_POST['perm'] ?? [];
        $posted = is_array($posted) ? array_map('strval', $posted) : [];
        $valid = array_column(db_all('SELECT slug FROM permissions'), 'slug');
        $sel = array_values(array_intersect($posted, $valid));
        if (Auth::isSuper()) return $sel;
        $mine = Auth::perms();
        return array_values(array_unique(array_merge(array_intersect($sel, $mine), array_diff($existing, $mine))));
    }

    private static function save(int $roleId, array $final, array $existing): string
    {
        $add = array_diff($final, $existing);
        $del = array_diff($existing, $final);
        db_exec('DELETE FROM role_permissions WHERE role_id = ?', [$roleId]);
        foreach ($final as $slug) {
            $pid = (int)db_val('SELECT id FROM permissions WHERE slug = ?', [$slug]);
            if ($pid) db_exec('INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES (?,?)', [$roleId, $pid]);
        }
        return mb_substr('+' . implode(',', $add) . ' -' . implode(',', $del), 0, 400);
    }

    public static function store(): void
    {
        Auth::need('roles.create');
        csrf_check();
        $name = post_str('name', 60);
        $desc = post_str('description', 200);
        $errors = [];
        if ($name === '') $errors['name'] = 'Nama peran wajib diisi.';
        elseif ((int)db_val('SELECT COUNT(*) FROM roles WHERE LOWER(name) = LOWER(?)', [$name])) $errors['name'] = 'Nama peran sudah ada.';
        $final = self::finalPerms([]);
        if ($errors) { self::form(null, $final, $errors, ['name' => $name, 'description' => $desc]); return; }

        $base = slugify($name);
        $slug = $base;
        $i = 2;
        while ((int)db_val('SELECT COUNT(*) FROM roles WHERE slug = ?', [$slug])) $slug = $base . '-' . $i++;
        $id = db_insert('roles', ['slug' => $slug, 'name' => $name, 'description' => $desc]);
        $detail = self::save($id, $final, []);
        audit('create', 'roles', $id, $name . ' ' . $detail);
        flash('success', 'Peran dibuat.');
        redirect('/admin/roles');
    }

    public static function update(string $id): void
    {
        Auth::need('roles.edit');
        csrf_check();
        $role = self::find((int)$id);
        self::guard($role);
        $isSystem = in_array($role['slug'], self::SYSTEM, true);
        $name = $isSystem ? $role['name'] : post_str('name', 60);
        $desc = post_str('description', 200);
        $errors = [];
        if ($name === '') $errors['name'] = 'Nama peran wajib diisi.';
        elseif ((int)db_val('SELECT COUNT(*) FROM roles WHERE LOWER(name) = LOWER(?) AND id != ?', [$name, $role['id']])) $errors['name'] = 'Nama peran sudah ada.';
        $existing = Auth::permsOfRole((int)$role['id']);
        $final = self::finalPerms($existing);
        if ($errors) { self::form($role, $final, $errors, ['name' => $name, 'description' => $desc]); return; }

        db_exec('UPDATE roles SET name = ?, description = ? WHERE id = ?', [$name, $desc, $role['id']]);
        $detail = self::save((int)$role['id'], $final, $existing);
        audit('update', 'roles', (int)$role['id'], $role['name'] . ' ' . $detail);
        flash('success', 'Peran disimpan. Perubahan izin langsung berlaku untuk semua pengguna peran ini.');
        redirect('/admin/roles');
    }

    public static function destroy(string $id): void
    {
        Auth::need('roles.delete');
        csrf_check();
        $role = self::find((int)$id);
        self::guard($role);
        if (in_array($role['slug'], self::SYSTEM, true)) { flash('error', 'Peran bawaan tidak dapat dihapus.'); redirect('/admin/roles'); }
        if ((int)db_val('SELECT COUNT(*) FROM users WHERE role_id = ?', [$role['id']]) > 0) {
            flash('error', 'Peran masih dipakai pengguna. Pindahkan penggunanya ke peran lain dulu.');
            redirect('/admin/roles');
        }
        db_exec('DELETE FROM roles WHERE id = ?', [$role['id']]);
        audit('delete', 'roles', (int)$role['id'], $role['name']);
        flash('success', 'Peran dihapus.');
        redirect('/admin/roles');
    }
}
