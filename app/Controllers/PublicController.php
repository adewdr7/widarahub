<?php
class PublicController
{
    private static function categories(string $type): array
    {
        return db_all('SELECT * FROM categories WHERE type = ? AND deleted_at IS NULL ORDER BY name', [$type]);
    }

    public static function home(): void
    {
        $posts = db_all('SELECT p.*, c.name AS category_name FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                         WHERE ' . pub_where('p') . ' ORDER BY p.published_at DESC LIMIT 6');
        $potentials = db_all('SELECT p.*, c.name AS category_name FROM potentials p LEFT JOIN categories c ON c.id = p.category_id
                              WHERE ' . pub_where('p') . ' ORDER BY p.published_at DESC LIMIT 6');
        $announcements = db_all('SELECT * FROM announcements WHERE ' . pub_where() . " AND (expires_at IS NULL OR expires_at >= ?)
                                 ORDER BY priority DESC, published_at DESC LIMIT 4", [date('Y-m-d')]);
        $events = db_all('SELECT * FROM events WHERE ' . pub_where() . ' AND starts_at >= ? ORDER BY starts_at ASC LIMIT 4', [date('Y-m-d 00:00:00')]);
        $stats = db_all('SELECT * FROM village_statistics WHERE is_verified = 1 AND deleted_at IS NULL ORDER BY sort_order, id');
        $quick = [
            ['Profil Desa', '/profil', 'assets/img/icons/ikon-profil.svg'],
            ['Berita', '/informasi', 'assets/img/icons/ikon-berita.svg'],
            ['Pelayanan', '/layanan', 'assets/img/icons/ikon-pelayanan.svg'],
            ['Potensi Desa', '/potensi', 'assets/img/icons/ikon-potensi.svg'],
            ['Pengumuman', '/pengumuman', 'assets/img/icons/ikon-pengumuman.svg'],
            ['Kontak', '/kontak', 'assets/img/icons/ikon-kontak.svg'],
        ];
        view('public/home', compact('posts', 'potentials', 'announcements', 'events', 'stats', 'quick') + ['title' => '']);
    }

    public static function profile(): void
    {
        $pages = db_all("SELECT * FROM pages WHERE section = 'profil' AND " . pub_where() . ' ORDER BY sort_order, id');
        view('public/profile', ['title' => 'Profil Desa', 'pages' => $pages]);
    }

    public static function page(string $slug): void
    {
        $page = db_one('SELECT * FROM pages WHERE slug = ? AND ' . pub_where(), [$slug]);
        if (!$page) abort(404);
        $siblings = $page['section'] === 'profil'
            ? db_all("SELECT slug, title FROM pages WHERE section = 'profil' AND " . pub_where() . ' ORDER BY sort_order, id') : [];
        view('public/page', ['title' => $page['title'], 'meta_description' => $page['meta_description'] ?: excerpt($page['body']),
            'page' => $page, 'siblings' => $siblings]);
    }

    public static function government(): void
    {
        $rows = db_all('SELECT * FROM government_members WHERE ' . pub_where() . ' ORDER BY sort_order, id');
        $groups = [];
        foreach (config('gov_units') as $k => $label) $groups[$k] = ['label' => $label, 'members' => []];
        foreach ($rows as $r) if (isset($groups[$r['unit']])) $groups[$r['unit']]['members'][] = $r;
        $groups = array_filter($groups, function ($g) { return !empty($g['members']); });
        view('public/government', ['title' => 'Pemerintahan Desa', 'groups' => $groups]);
    }

    public static function posts(): void
    {
        $q = qs('q');
        $cat = qs('kategori');
        $where = [pub_where('p')];
        $params = [];
        if ($q !== '') { $where[] = '(p.title LIKE ? OR p.excerpt LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
        if ($cat !== '') { $where[] = 'c.slug = ?'; $params[] = $cat; }
        $w = implode(' AND ', $where);
        $total = (int)db_val("SELECT COUNT(*) FROM posts p LEFT JOIN categories c ON c.id = p.category_id WHERE $w", $params);
        $pg = paginate($total, (int)config('per_page'), (int)qs('page') ?: 1);
        $posts = db_all("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                         WHERE $w ORDER BY p.published_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}", $params);
        view('public/posts', ['title' => 'Informasi & Berita', 'posts' => $posts, 'pg' => $pg, 'q' => $q, 'cat' => $cat, 'categories' => self::categories('post')]);
    }

    public static function post(string $slug): void
    {
        $post = db_one('SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                        WHERE p.slug = ? AND ' . pub_where('p'), [$slug]);
        if (!$post) abort(404);
        $related = db_all('SELECT p.*, c.name AS category_name FROM posts p LEFT JOIN categories c ON c.id = p.category_id
                           WHERE p.id != ? AND p.category_id = ? AND ' . pub_where('p') . ' ORDER BY p.published_at DESC LIMIT 3',
                           [$post['id'], $post['category_id'] ?? 0]);
        view('public/post', ['title' => $post['title'], 'meta_description' => $post['excerpt'] ?: excerpt($post['body']),
            'og_image' => media_url($post['thumbnail']), 'post' => $post, 'related' => $related]);
    }

    public static function announcements(): void
    {
        $total = (int)db_val('SELECT COUNT(*) FROM announcements WHERE ' . pub_where());
        $pg = paginate($total, 10, (int)qs('page') ?: 1);
        $items = db_all('SELECT * FROM announcements WHERE ' . pub_where() . " ORDER BY priority DESC, published_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}");
        view('public/announcements', ['title' => 'Pengumuman', 'items' => $items, 'pg' => $pg]);
    }

    public static function events(): void
    {
        $upcoming = db_all('SELECT * FROM events WHERE ' . pub_where() . ' AND starts_at >= ? ORDER BY starts_at ASC', [date('Y-m-d 00:00:00')]);
        $past = db_all('SELECT * FROM events WHERE ' . pub_where() . ' AND starts_at < ? ORDER BY starts_at DESC LIMIT 10', [date('Y-m-d 00:00:00')]);
        view('public/events', ['title' => 'Agenda Desa', 'upcoming' => $upcoming, 'past' => $past]);
    }

    public static function potentials(): void
    {
        $cat = qs('kategori');
        $where = [pub_where('p')];
        $params = [];
        if ($cat !== '') { $where[] = 'c.slug = ?'; $params[] = $cat; }
        $w = implode(' AND ', $where);
        $total = (int)db_val("SELECT COUNT(*) FROM potentials p LEFT JOIN categories c ON c.id = p.category_id WHERE $w", $params);
        $pg = paginate($total, (int)config('per_page'), (int)qs('page') ?: 1);
        $items = db_all("SELECT p.*, c.name AS category_name FROM potentials p LEFT JOIN categories c ON c.id = p.category_id
                         WHERE $w ORDER BY p.published_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}", $params);
        $markers = [];
        $v = village();
        if (is_numeric($v['maps_lat'] ?? null) && is_numeric($v['maps_lng'] ?? null)) {
            $markers[] = ['lat' => (float)$v['maps_lat'], 'lng' => (float)$v['maps_lng'], 'title' => 'Kantor ' . $v['name']];
        }
        foreach (db_all("SELECT p.title, p.slug, p.lat, p.lng FROM potentials p LEFT JOIN categories c ON c.id = p.category_id
                         WHERE $w AND p.lat IS NOT NULL AND p.lng IS NOT NULL", $params) as $r) {
            $markers[] = ['lat' => (float)$r['lat'], 'lng' => (float)$r['lng'], 'title' => $r['title'], 'url' => '/potensi/' . $r['slug']];
        }
        view('public/potentials', ['title' => 'Potensi Desa', 'items' => $items, 'pg' => $pg, 'cat' => $cat, 'categories' => self::categories('potential'), 'markers' => count($markers) > 1 ? $markers : []]);
    }

    public static function potential(string $slug): void
    {
        $item = db_one('SELECT p.*, c.name AS category_name FROM potentials p LEFT JOIN categories c ON c.id = p.category_id
                        WHERE p.slug = ? AND ' . pub_where('p'), [$slug]);
        if (!$item) abort(404);
        view('public/potential', ['title' => $item['title'], 'meta_description' => excerpt($item['description']),
            'og_image' => media_url($item['thumbnail']), 'item' => $item]);
    }

    public static function documents(): void
    {
        $cat = qs('kategori');
        $where = [pub_where('d')];
        $params = [];
        if ($cat !== '') { $where[] = 'c.slug = ?'; $params[] = $cat; }
        $w = implode(' AND ', $where);
        $total = (int)db_val("SELECT COUNT(*) FROM public_documents d LEFT JOIN categories c ON c.id = d.category_id WHERE $w", $params);
        $pg = paginate($total, 12, (int)qs('page') ?: 1);
        $items = db_all("SELECT d.*, c.name AS category_name FROM public_documents d LEFT JOIN categories c ON c.id = d.category_id
                         WHERE $w ORDER BY d.year DESC, d.published_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}", $params);
        view('public/documents', ['title' => 'Transparansi & Dokumen Publik', 'items' => $items, 'pg' => $pg, 'cat' => $cat, 'categories' => self::categories('document')]);
    }

    public static function services(): void
    {
        $items = db_all('SELECT * FROM services WHERE ' . pub_where() . ' ORDER BY sort_order, id');
        view('public/services', ['title' => 'Layanan Publik', 'items' => $items]);
    }

    public static function gallery(): void
    {
        $cat = qs('kategori');
        $where = [pub_where('g')];
        $params = [];
        if ($cat !== '') { $where[] = 'c.slug = ?'; $params[] = $cat; }
        $w = implode(' AND ', $where);
        $total = (int)db_val("SELECT COUNT(*) FROM galleries g LEFT JOIN categories c ON c.id = g.category_id WHERE $w", $params);
        $pg = paginate($total, 12, (int)qs('page') ?: 1);
        $items = db_all("SELECT g.*, c.name AS category_name FROM galleries g LEFT JOIN categories c ON c.id = g.category_id
                         WHERE $w ORDER BY g.published_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}", $params);
        view('public/gallery', ['title' => 'Galeri Desa', 'items' => $items, 'pg' => $pg, 'cat' => $cat, 'categories' => self::categories('gallery')]);
    }

    private static function interestCounts(string $type): array
    {
        $o = [];
        foreach (db_all("SELECT ref_id, COUNT(*) AS c FROM interests WHERE ref_type = ? AND deleted_at IS NULL AND status != 'dibatalkan' GROUP BY ref_id", [$type]) as $r) {
            $o[(int)$r['ref_id']] = (int)$r['c'];   // hanya jumlah yang tampil di publik, bukan identitas
        }
        return $o;
    }

    public static function trainings(): void
    {
        $items = db_all('SELECT t.*, g.title AS goal_title FROM trainings t LEFT JOIN village_goals g ON g.id = t.goal_id
                         WHERE ' . pub_where('t') . ' ORDER BY (t.starts_at IS NULL), t.starts_at ASC');
        view('public/trainings', ['title' => 'Pelatihan & Pengembangan SDM', 'items' => $items, 'counts' => self::interestCounts('training')]);
    }

    public static function interestSubmit(): void
    {
        csrf_check();
        $type = post_str('ref_type', 20);
        $id = (int)post_str('ref_id', 10);
        $back = $type === 'training' ? '/pelatihan' : '/keuangan#kebutuhan';
        if (post_str('website') !== '') redirect($back);   // honeypot anti-bot
        if (!in_array($type, ['need', 'training'], true)) abort(404);
        if (time() - (int)($_SESSION['last_interest'] ?? 0) < 20) { flash('error', 'Mohon tunggu sebentar sebelum mengirim lagi.'); redirect($back); }

        $ref = $type === 'training'
            ? db_one('SELECT * FROM trainings WHERE id = ? AND ' . pub_where(), [$id])
            : db_one('SELECT * FROM village_needs WHERE id = ? AND ' . pub_where(), [$id]);
        if (!$ref) abort(404);

        if ($type === 'training') {
            $taken = (int)(self::interestCounts('training')[$id] ?? 0);
            if (!$ref['is_open']) { flash('error', 'Pendaftaran untuk pelatihan ini sudah ditutup.'); redirect($back); }
            if ((int)$ref['quota'] > 0 && $taken >= (int)$ref['quota']) { flash('error', 'Kuota pelatihan sudah penuh.'); redirect($back); }
        } elseif ($ref['status_need'] === 'tersedia') {
            flash('error', 'Layanan ini sudah tersedia.'); redirect($back);
        }

        $name = post_str('name', 100);
        $contact = post_str('contact', 100);
        if ($name === '' || mb_strlen($contact) < 6 || post_str('consent', 5) === '') {
            flash('error', 'Nama, kontak (min. 6 karakter), dan persetujuan penggunaan data wajib diisi.');
            redirect($back);
        }
        if ((int)db_val('SELECT COUNT(*) FROM interests WHERE ref_type = ? AND ref_id = ? AND contact = ? AND deleted_at IS NULL', [$type, $id, $contact])) {
            flash('error', 'Kontak ini sudah terdaftar untuk kegiatan tersebut.');
            redirect($back);
        }
        db_insert('interests', ['ref_type' => $type, 'ref_id' => $id, 'ref_title' => mb_substr($ref['title'], 0, 200), 'name' => $name, 'contact' => $contact,
            'note' => post_str('note', 500), 'consent' => 1, 'status' => 'baru', 'created_at' => now(), 'updated_at' => now()]);
        $_SESSION['last_interest'] = time();
        flash('success', 'Terima kasih, minat Anda tercatat. Petugas desa akan menghubungi Anda.');
        redirect($back);
    }

    public static function finance(): void
    {
        $years = array_map('intval', array_column(db_all('SELECT DISTINCT year FROM village_budgets WHERE ' . pub_where() . ' ORDER BY year DESC'), 'year'));
        $year = (int)qs('tahun');
        if (!in_array($year, $years, true)) $year = $years ? $years[0] : (int)date('Y');

        $rows = db_all('SELECT * FROM village_budgets WHERE ' . pub_where() . ' AND year = ? ORDER BY kind, sector, id', [$year]);
        $sum = [];
        foreach (array_keys(config('finance_kinds')) as $k) $sum[$k] = ['planned' => 0, 'realized' => 0];
        $groups = [];
        foreach ($rows as $r) {
            if (!isset($sum[$r['kind']])) continue;
            $sum[$r['kind']]['planned'] += (int)$r['planned'];
            $sum[$r['kind']]['realized'] += (int)$r['realized'];
            $groups[$r['kind']][$r['sector'] !== '' ? $r['sector'] : 'Lainnya'][] = $r;
        }
        $goals = db_all('SELECT * FROM village_goals WHERE ' . pub_where() . ' ORDER BY pillar, id');
        $needs = db_all('SELECT * FROM village_needs WHERE ' . pub_where() . ' ORDER BY priority DESC, id DESC');
        $interestCounts = self::interestCounts('need');
        view('public/finance', compact('years', 'year', 'sum', 'groups', 'goals', 'needs', 'interestCounts') + ['title' => 'Keuangan & Tujuan Desa']);
    }

    public static function contact(): void
    {
        $ticket = null;
        $lookup = strtoupper(qs('tiket'));
        if ($lookup !== '') {
            // hanya status yang ditampilkan, isi laporan tidak dibuka ke publik
            $ticket = db_one('SELECT ticket, type, subject, status, response, created_at FROM complaints WHERE ticket = ? AND deleted_at IS NULL', [$lookup]) ?: false;
        }
        view('public/contact', ['title' => 'Kontak & Pengaduan', 'ticket' => $ticket, 'lookup' => $lookup]);
    }

    public static function contactSubmit(): void
    {
        csrf_check();
        if (post_str('website') !== '') redirect('/kontak');   // honeypot anti-bot
        if (time() - (int)($_SESSION['last_complaint'] ?? 0) < 30) {
            flash('error', 'Mohon tunggu sebentar sebelum mengirim laporan lagi.');
            redirect('/kontak#form');
        }
        $type = post_str('type', 20);
        if (!in_array($type, ['pengaduan', 'aspirasi', 'usulan'], true)) $type = 'pengaduan';
        $subject = post_str('subject', 150);
        $message = post_str('message', 3000);
        if ($subject === '' || mb_strlen($message) < 10) {
            flash('error', 'Perihal wajib diisi dan isi laporan minimal 10 karakter.');
            redirect('/kontak#form');
        }
        $ticket = 'WH-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        db_insert('complaints', ['ticket' => $ticket, 'type' => $type, 'name' => post_str('name', 100), 'contact' => post_str('contact', 100),
            'subject' => $subject, 'message' => $message, 'status' => 'baru', 'created_at' => now(), 'updated_at' => now()]);
        $_SESSION['last_complaint'] = time();
        flash('success', 'Laporan diterima. Simpan nomor tiket Anda: ' . $ticket);
        redirect('/kontak?tiket=' . $ticket);
    }

    public static function search(): void
    {
        $q = qs('q');
        $results = [];
        if ($q !== '') {
            $like = "%$q%";
            foreach (db_all('SELECT title, slug, excerpt AS summary, published_at FROM posts WHERE ' . pub_where() . ' AND (title LIKE ? OR excerpt LIKE ? OR body LIKE ?) LIMIT 10', [$like, $like, $like]) as $r)
                $results[] = ['type' => 'Berita', 'title' => $r['title'], 'url' => '/informasi/' . $r['slug'], 'summary' => $r['summary']];
            foreach (db_all('SELECT title, slug, description FROM potentials WHERE ' . pub_where() . ' AND (title LIKE ? OR description LIKE ?) LIMIT 10', [$like, $like]) as $r)
                $results[] = ['type' => 'Potensi', 'title' => $r['title'], 'url' => '/potensi/' . $r['slug'], 'summary' => excerpt($r['description'], 140)];
            foreach (db_all('SELECT title, slug, section, body FROM pages WHERE ' . pub_where() . ' AND (title LIKE ? OR body LIKE ?) LIMIT 10', [$like, $like]) as $r)
                $results[] = ['type' => 'Halaman', 'title' => $r['title'], 'url' => ($r['section'] === 'profil' ? '/profil/' : '/halaman/') . $r['slug'], 'summary' => excerpt($r['body'], 140)];
            foreach (db_all('SELECT title, slug, body FROM announcements WHERE ' . pub_where() . ' AND (title LIKE ? OR body LIKE ?) LIMIT 10', [$like, $like]) as $r)
                $results[] = ['type' => 'Pengumuman', 'title' => $r['title'], 'url' => '/pengumuman', 'summary' => excerpt($r['body'], 140)];
        }
        view('public/search', ['title' => 'Pencarian', 'q' => $q, 'results' => $results]);
    }

    public static function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $base = base_url();
        $urls = [];
        foreach (config('nav') as $n) $urls[] = $n[1];
        array_push($urls, '/pengumuman', '/agenda', '/galeri', '/pelatihan');
        foreach (db_all('SELECT slug FROM posts WHERE ' . pub_where()) as $r) $urls[] = '/informasi/' . $r['slug'];
        foreach (db_all('SELECT slug FROM potentials WHERE ' . pub_where()) as $r) $urls[] = '/potensi/' . $r['slug'];
        foreach (db_all('SELECT slug, section FROM pages WHERE ' . pub_where()) as $r) $urls[] = ($r['section'] === 'profil' ? '/profil/' : '/halaman/') . $r['slug'];
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach (array_unique($urls) as $u) echo '  <url><loc>' . e($base . $u) . '</loc></url>' . "\n";
        echo '</urlset>';
    }

    public static function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\nDisallow: /admin\nSitemap: " . base_url() . "/sitemap.xml\n";
    }
}
