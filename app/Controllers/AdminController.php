<?php
class AdminController
{
    public static function dashboard(): void
    {
        Auth::needLogin();
        $workflowTables = [
            'pages' => 'Halaman', 'government_members' => 'Pemerintahan', 'posts' => 'Berita', 'announcements' => 'Pengumuman',
            'events' => 'Agenda', 'potentials' => 'Potensi', 'public_documents' => 'Dokumen', 'services' => 'Layanan', 'galleries' => 'Galeri',
            'village_budgets' => 'Keuangan', 'village_goals' => 'Sasaran', 'village_needs' => 'Kebutuhan', 'trainings' => 'Pelatihan',
        ];
        $resKeys = ['pages' => 'pages', 'government_members' => 'government', 'posts' => 'posts', 'announcements' => 'announcements',
                    'events' => 'events', 'potentials' => 'potentials', 'public_documents' => 'documents', 'services' => 'services', 'galleries' => 'gallery',
                    'village_budgets' => 'budgets', 'village_goals' => 'goals', 'village_needs' => 'needs', 'trainings' => 'trainings'];

        $count = function (string $t, string $extra = ''): int {
            return (int)db_val("SELECT COUNT(*) FROM $t WHERE deleted_at IS NULL $extra");
        };
        $stats = [
            'Total berita' => $count('posts'),
            'Total pengumuman' => $count('announcements'),
            'Total halaman' => $count('pages'),
            'Draft' => 0, 'Menunggu persetujuan' => 0, 'Terpublikasi' => 0,
        ];
        if (Auth::can('interests.view')) $stats['Minat warga baru'] = $count('interests', "AND status = 'baru'");
        $pending = [];
        foreach ($workflowTables as $t => $label) {
            $stats['Draft'] += $count($t, "AND status = 'draft'");
            $stats['Menunggu persetujuan'] += $count($t, "AND status IN ('submitted','review')");
            $stats['Terpublikasi'] += $count($t, "AND status = 'published'");
            $titleCol = $t === 'government_members' ? 'name' : 'title';
            foreach (db_all("SELECT id, $titleCol AS t, status FROM $t WHERE deleted_at IS NULL AND status IN ('submitted','review') ORDER BY id DESC LIMIT 5") as $r) {
                $pending[] = ['label' => $label, 'title' => $r['t'], 'status' => $r['status'], 'url' => '/admin/r/' . $resKeys[$t] . '/' . $r['id'] . '/edit'];
            }
        }

        $v = village();
        $marker = config('demo_marker');
        $pageDone = function (string $slug): bool {
            $b = (string)db_val("SELECT body FROM pages WHERE slug = ? AND deleted_at IS NULL AND status = 'published'", [$slug]);
            return strlen(strip_tags($b)) > 40 && !is_demo($b);
        };
        $govReal = (int)db_val("SELECT COUNT(*) FROM government_members WHERE deleted_at IS NULL AND status='published' AND name NOT LIKE ?", ['%' . $marker . '%']);
        $completeness = [
            'Identitas desa' => !empty($v['is_verified']) && !empty($v['name']),
            'Profil geografis' => $pageDone('kondisi-geografis'),
            'Struktur pemerintahan' => $govReal > 0,
            'Sejarah desa' => $pageDone('sejarah-desa'),
            'Informasi kontak' => !empty($v['phone']) && !empty($v['address']),
        ];

        $events = db_all("SELECT title, starts_at, location FROM events WHERE deleted_at IS NULL AND starts_at >= ? ORDER BY starts_at ASC LIMIT 5", [date('Y-m-d H:i:s')]);
        $recent = Auth::can('audit.view') ? db_all('SELECT * FROM audit_logs ORDER BY id DESC LIMIT 8') : [];

        $adminPw = db_one("SELECT password_hash FROM users WHERE username = 'admin' AND deleted_at IS NULL");
        $defaultPw = $adminPw && password_verify('admin12345', $adminPw['password_hash']) && Auth::user()['username'] === 'admin';

        view('admin/dashboard', [
            'title' => 'Overview', 'stats' => $stats, 'pending' => $pending, 'completeness' => $completeness,
            'events' => $events, 'recent' => $recent, 'defaultPw' => $defaultPw,
        ], 'admin');
    }

    public static function audit(): void
    {
        Auth::need('audit.view');
        $q = qs('q');
        $where = '1=1';
        $params = [];
        if ($q !== '') { $where = '(username LIKE ? OR action LIKE ? OR table_name LIKE ? OR detail LIKE ?)'; $params = array_fill(0, 4, "%$q%"); }
        $total = (int)db_val("SELECT COUNT(*) FROM audit_logs WHERE $where", $params);
        $pg = paginate($total, 25, (int)qs('page') ?: 1);
        $rows = db_all("SELECT * FROM audit_logs WHERE $where ORDER BY id DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}", $params);
        view('admin/audit', ['title' => 'Audit Log', 'rows' => $rows, 'pg' => $pg, 'q' => $q], 'admin');
    }
}
