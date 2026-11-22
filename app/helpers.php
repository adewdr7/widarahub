<?php
// ---------- Polyfill mbstring (jaga-jaga bila ekstensi tidak ada) ----------
if (!function_exists('mb_strlen')) { function mb_strlen($s) { return strlen($s); } }
if (!function_exists('mb_substr')) { function mb_substr($s, $a, $l = null) { return $l === null ? substr($s, $a) : substr($s, $a, $l); } }

function config(string $key, $default = null)
{
    $v = $GLOBALS['config'];
    foreach (explode('.', $key) as $k) {
        if (!is_array($v) || !array_key_exists($k, $v)) return $default;
        $v = $v[$k];
    }
    return $v;
}

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function now(): string { return date('Y-m-d H:i:s'); }
function url(string $path = ''): string { return '/' . ltrim($path, '/'); }
function asset(string $path): string { return '/assets/' . ltrim($path, '/'); }

/** URL file di folder public; bila file tidak ada, pakai placeholder. */
function media_url(?string $path, string $fallback = 'assets/img/placeholder.svg'): string
{
    $path = ltrim((string)$path, '/');
    if ($path !== '' && strpos($path, '..') === false && is_file(BASE_PATH . '/public/' . $path)) {
        return '/' . $path;
    }
    return '/' . ltrim($fallback, '/');
}

function base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8080');
}

function current_path(): string { return rtrim((string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') ?: '/'; }

function redirect(string $to): void { header('Location: ' . $to); exit; }

// ---------- Flash & input ----------
function flash(string $type, string $msg): void { $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg]; }
function flashes(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }

function qs(string $key): string { $v = $_GET[$key] ?? ''; return is_string($v) ? trim($v) : ''; }
function post_str(string $key, int $max = 255): string
{
    $v = $_POST[$key] ?? '';
    return is_string($v) ? mb_substr(trim($v), 0, $max) : '';
}

// ---------- CSRF ----------
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check(): void
{
    $t = $_POST['_csrf'] ?? '';
    if (!is_string($t) || !hash_equals($_SESSION['csrf'] ?? '', $t)) {
        abort(419, 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }
}

// ---------- View ----------
function view(string $__view, array $__data = [], ?string $__layout = 'public'): void
{
    extract($__data, EXTR_SKIP);
    ob_start();
    require BASE_PATH . '/views/' . $__view . '.php';
    $content = ob_get_clean();
    if ($__layout) {
        require BASE_PATH . '/views/layouts/' . $__layout . '.php';
    } else {
        echo $content;
    }
}

function partial(string $__p, array $__v = []): void
{
    extract($__v, EXTR_SKIP);
    require BASE_PATH . '/views/partials/' . $__p . '.php';
}

function abort(int $code = 404, string $message = ''): void
{
    http_response_code($code);
    $titles = [403 => 'Akses ditolak', 404 => 'Halaman tidak ditemukan', 419 => 'Sesi kedaluwarsa', 500 => 'Terjadi kesalahan'];
    $title = $titles[$code] ?? 'Kesalahan';
    $message = $message !== '' ? $message : $title;
    $isAdmin = strpos(current_path(), '/admin') === 0 && Auth::check();
    view('errors/error', compact('code', 'title', 'message'), $isAdmin ? 'admin' : 'public');
    exit;
}

// ---------- Teks & tanggal ----------
function slugify(string $t): string
{
    $t = strtolower(trim($t));
    $t = preg_replace('/[^a-z0-9]+/', '-', $t);
    $t = trim((string)$t, '-');
    return $t !== '' ? $t : 'item';
}

function id_date(?string $d, bool $time = false): string
{
    if (!$d) return '';
    $ts = strtotime($d);
    if (!$ts) return '';
    $m = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $s = date('j', $ts) . ' ' . $m[(int)date('n', $ts)] . ' ' . date('Y', $ts);
    return $time ? $s . ', ' . date('H:i', $ts) . ' WIB' : $s;
}

function excerpt(?string $html, int $len = 160): string
{
    $t = trim((string)preg_replace('/\s+/', ' ', strip_tags((string)$html)));
    return mb_strlen($t) > $len ? mb_substr($t, 0, $len - 1) . '…' : $t;
}

/** Sanitasi HTML dasar (allowlist tag). Untuk produksi disarankan HTMLPurifier. */
function clean_html(?string $html): string
{
    $html = strip_tags((string)$html, '<p><br><strong><b><em><i><u><ul><ol><li><a><h2><h3><h4><blockquote><img><figure><figcaption><table><thead><tbody><tr><th><td>');
    $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/\sstyle\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*(javascript|data|vbscript):[^"\']*\2/i', '$1="#"', $html);
    return (string)$html;
}

function is_demo(?string $text): bool { return strpos((string)$text, config('demo_marker')) !== false; }
function status_label(?string $s): string { return config('status_labels')[$s] ?? (string)$s; }

// ---------- Pagination ----------
function paginate(int $total, int $perPage, int $page): array
{
    $pages = max(1, (int)ceil($total / max(1, $perPage)));
    $page = min(max(1, $page), $pages);
    return ['page' => $page, 'pages' => $pages, 'per' => $perPage, 'offset' => ($page - 1) * $perPage, 'total' => $total];
}
function page_url(int $n): string
{
    $q = $_GET;
    $q['page'] = $n;
    return current_path() . '?' . http_build_query($q);
}

// ---------- Data situs ----------
function village(): array
{
    static $v = null;
    if ($v === null) $v = db_one('SELECT * FROM villages WHERE id = 1') ?: [];
    return $v;
}

function setting(string $key, $default = '')
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db_all('SELECT setting_key, setting_value FROM site_settings') as $r) $cache[$r['setting_key']] = $r['setting_value'];
    }
    return (isset($cache[$key]) && $cache[$key] !== '') ? $cache[$key] : $default;
}

/** Kondisi WHERE: hanya konten terpublikasi, tidak dihapus, dan jadwal terbit sudah lewat. */
function pub_where(string $alias = ''): string
{
    $p = $alias !== '' ? $alias . '.' : '';
    return "{$p}status = 'published' AND {$p}deleted_at IS NULL AND ({$p}published_at IS NULL OR {$p}published_at <= '" . now() . "')";
}

function audit(string $action, string $table = '', $id = null, string $detail = ''): void
{
    $u = Auth::user();
    db_exec(
        'INSERT INTO audit_logs (user_id, username, action, table_name, record_id, detail, ip, created_at) VALUES (?,?,?,?,?,?,?,?)',
        [$u['id'] ?? null, $u['username'] ?? '-', $action, $table, $id, $detail, $_SERVER['REMOTE_ADDR'] ?? '', now()]
    );
}
