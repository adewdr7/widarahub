<?php
class Auth
{
    private static ?array $user = null;
    private static bool $loaded = false;
    private static ?array $perms = null;   // cache per-request: perubahan izin langsung berlaku

    public static function user(): ?array
    {
        if (!self::$loaded) {
            self::$loaded = true;
            if (!empty($_SESSION['uid'])) {
                self::$user = db_one(
                    'SELECT u.*, r.slug AS role_slug, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id
                     WHERE u.id = ? AND u.is_active = 1 AND u.deleted_at IS NULL',
                    [$_SESSION['uid']]
                );
                if (!self::$user) unset($_SESSION['uid']);
            }
        }
        return self::$user;
    }

    public static function check(): bool { return self::user() !== null; }
    public static function isSuper(): bool { $u = self::user(); return $u && $u['role_slug'] === 'super_admin'; }

    /** Daftar izin pengguna saat ini (Super Admin = semua izin yang ada). */
    public static function perms(): array
    {
        $u = self::user();
        if (!$u) return [];
        if (self::$perms === null) {
            self::$perms = $u['role_slug'] === 'super_admin'
                ? array_column(db_all('SELECT slug FROM permissions'), 'slug')
                : self::permsOfRole((int)$u['role_id']);
        }
        return self::$perms;
    }

    public static function permsOfRole(int $roleId): array
    {
        $rows = db_all('SELECT p.slug FROM role_permissions rp JOIN permissions p ON p.id = rp.permission_id WHERE rp.role_id = ?', [$roleId]);
        return array_column($rows, 'slug');
    }

    public static function can(string $perm): bool
    {
        $u = self::user();
        if (!$u) return false;
        if ($u['role_slug'] === 'super_admin') return true;
        return in_array($perm, self::perms(), true);
    }

    public static function needLogin(): void
    {
        if (!self::check()) redirect('/admin/login');
    }

    public static function need(string $perm): void
    {
        self::needLogin();
        if (!self::can($perm)) abort(403, 'Anda tidak memiliki izin untuk tindakan ini.');
    }

    /** Mengembalikan pesan error, atau null bila berhasil. */
    public static function attempt(string $username, string $password): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $since = date('Y-m-d H:i:s', time() - 600);
        $fails = (int)db_val('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at >= ?', [$ip, $since]);
        if ($fails >= 5) return 'Terlalu banyak percobaan login. Coba lagi dalam 10 menit.';

        $u = db_one('SELECT * FROM users WHERE username = ? AND is_active = 1 AND deleted_at IS NULL', [$username]);
        if (!$u || !password_verify($password, $u['password_hash'])) {
            db_exec('INSERT INTO login_attempts (ip, username, attempted_at) VALUES (?,?,?)', [$ip, $username, now()]);
            return 'Username atau kata sandi salah.';
        }
        db_exec('DELETE FROM login_attempts WHERE ip = ?', [$ip]);
        session_regenerate_id(true);
        $_SESSION['uid'] = (int)$u['id'];
        self::$loaded = false;
        self::$perms = null;
        db_exec('UPDATE users SET last_login_at = ? WHERE id = ?', [now(), $u['id']]);
        audit('login', 'users', $u['id']);
        return null;
    }

    public static function logout(): void
    {
        audit('logout');
        $_SESSION = [];
        session_regenerate_id(true);
        self::$user = null;
        self::$loaded = false;
        self::$perms = null;
    }
}
