<?php
/** Membuat tabel + data contoh saat database belum ada. Semua konten contoh bertanda [DEMO]. */
class Installer
{
    public static function run(PDO $pdo): void
    {
        $pdo->exec(file_get_contents(BASE_PATH . '/database/schema.sql'));
        self::seedAccess();
        self::seedContent();
        self::migrateKeuangan();
        self::migrateSdm();
        self::migrateMap();
    }

    /** Modul Keuangan & Tujuan Desa. Aman dijalankan pada database lama (tidak menghapus data). */
    public static function migrateKeuangan(): void
    {
        db()->exec(file_get_contents(BASE_PATH . '/database/001_keuangan.sql'));

        $mods = ['budgets', 'goals', 'needs'];
        $all = ['view', 'create', 'edit', 'delete', 'publish'];
        foreach ($mods as $m) foreach ($all as $a) db_exec('INSERT OR IGNORE INTO permissions (slug) VALUES (?)', ["$m.$a"]);
        $grants = ['super_admin' => $all, 'admin_desa' => $all, 'editor' => ['view', 'create', 'edit'], 'operator' => ['view'], 'viewer' => ['view']];
        foreach ($grants as $role => $acts) {
            $rid = (int)db_val('SELECT id FROM roles WHERE slug = ?', [$role]);
            if (!$rid) continue;
            foreach ($mods as $m) foreach ($acts as $a) {
                $pid = (int)db_val('SELECT id FROM permissions WHERE slug = ?', ["$m.$a"]);
                if ($pid) db_exec('INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES (?,?)', [$rid, $pid]);
            }
        }

        if ((int)db_val('SELECT COUNT(*) FROM village_budgets') > 0) return;
        $demo = config('demo_marker');
        $now = now();
        $pub = ['status' => 'published', 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now];
        $year = (int)date('Y');
        foreach ([
            ['pendapatan', 'Pendapatan Asli Desa', "Contoh pendapatan $demo", 20000000, 15000000],
            ['pendapatan', 'Transfer', "Contoh dana transfer $demo", 300000000, 300000000],
            ['belanja', 'Penyelenggaraan Pemerintahan Desa', "Contoh belanja pemerintahan $demo", 150000000, 90000000],
            ['belanja', 'Pelaksanaan Pembangunan Desa', "Contoh belanja pembangunan $demo", 120000000, 40000000],
            ['belanja', 'Pemberdayaan Masyarakat Desa', "Contoh belanja pemberdayaan $demo", 50000000, 20000000],
        ] as [$kind, $sector, $title, $planned, $realized]) {
            db_insert('village_budgets', ['year' => $year, 'kind' => $kind, 'sector' => $sector, 'title' => $title,
                'planned' => $planned, 'realized' => $realized, 'note' => "$demo Angka contoh, bukan data resmi."] + $pub);
        }
        foreach ([
            ['ekonomi', "Contoh sasaran ekonomi desa $demo", 'Jumlah usaha jasa milik warga', 2, 4, 10, 'usaha'],
            ['sdm', "Contoh sasaran SDM $demo", 'Warga mengikuti pelatihan keterampilan', 0, 30, 100, 'orang'],
            ['pendidikan', "Contoh sasaran pendidikan $demo", 'Anak usia sekolah yang bersekolah', 80, 85, 100, '%'],
            ['infrastruktur', "Contoh sasaran infrastruktur $demo", 'Ruas jalan lingkungan diperbaiki', 1, 3, 8, 'ruas'],
            ['layanan', "Contoh sasaran keragaman layanan $demo", 'Jenis layanan tersedia di desa', 3, 5, 10, 'jenis'],
        ] as [$pillar, $title, $ind, $base, $cur, $target, $unit]) {
            db_insert('village_goals', ['pillar' => $pillar, 'title' => $title, 'description' => "$demo Penjelasan contoh.", 'indicator' => $ind,
                'baseline_value' => $base, 'current_value' => $cur, 'target_value' => $target, 'unit' => $unit, 'target_year' => $year + 3] + $pub);
        }
        foreach ([
            ['usaha_jasa', "Contoh jasa yang belum tersedia di desa $demo", 'dibutuhkan', 2],
            ['layanan', "Contoh layanan yang sedang dirintis $demo", 'dirintis', 1],
            ['fasilitas', "Contoh fasilitas yang sudah tersedia $demo", 'tersedia', 0],
        ] as [$cat, $title, $st, $prio]) {
            db_insert('village_needs', ['category' => $cat, 'title' => $title, 'description' => "$demo Penjelasan contoh peluang bagi warga.",
                'status_need' => $st, 'priority' => $prio] + $pub);
        }
    }

    /** Pelatihan SDM, minat warga (data privat), dan kolom produk UMKM pada Potensi Desa. */
    public static function migrateSdm(): void
    {
        db()->exec(file_get_contents(BASE_PATH . '/database/002_sdm_minat.sql'));

        $all = ['view', 'create', 'edit', 'delete', 'publish'];
        $mods = ['trainings' => $all, 'interests' => ['view', 'edit', 'delete']];
        foreach ($mods as $m => $acts) foreach ($acts as $a) db_exec('INSERT OR IGNORE INTO permissions (slug) VALUES (?)', ["$m.$a"]);
        // Data minat warga berisi nama & kontak: hanya Super Admin dan Admin Desa.
        $grants = [
            'super_admin' => $mods,
            'admin_desa'  => $mods,
            'editor'      => ['trainings' => ['view', 'create', 'edit']],
            'operator'    => ['trainings' => ['view', 'create', 'edit']],
            'viewer'      => ['trainings' => ['view']],
        ];
        foreach ($grants as $role => $map) {
            $rid = (int)db_val('SELECT id FROM roles WHERE slug = ?', [$role]);
            if (!$rid) continue;
            foreach ($map as $m => $acts) foreach ($acts as $a) {
                $pid = (int)db_val('SELECT id FROM permissions WHERE slug = ?', ["$m.$a"]);
                if ($pid) db_exec('INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES (?,?)', [$rid, $pid]);
            }
        }

        $demo = config('demo_marker');
        $now = now();
        db_exec("UPDATE potentials SET products = ?, price_range = ? WHERE slug = 'contoh-potensi-2' AND products = ''",
            ["$demo Contoh produk/jasa yang ditawarkan.", "$demo Contoh kisaran harga"]);
        if ((int)db_val('SELECT COUNT(*) FROM trainings') > 0) return;
        $goal = (int)db_val("SELECT id FROM village_goals WHERE pillar = 'sdm' AND deleted_at IS NULL ORDER BY id LIMIT 1");
        foreach ([["Contoh pelatihan keterampilan 1 $demo", 14, 20], ["Contoh pelatihan keterampilan 2 $demo", 30, 0]] as [$t, $d, $q]) {
            db_insert('trainings', ['title' => $t, 'description' => "$demo Deskripsi pelatihan contoh.", 'goal_id' => $goal ?: null,
                'starts_at' => date('Y-m-d 09:00:00', strtotime("+$d days")), 'location' => 'Balai Desa', 'quota' => $q, 'is_open' => 1,
                'status' => 'published', 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    /** Koordinat peta OpenStreetMap: kantor desa (villages) dan potensi desa. */
    public static function migrateMap(): void
    {
        db()->exec(file_get_contents(BASE_PATH . '/database/003_peta.sql'));
        // Nilai awal: Kantor Kepala Desa Widarasari (dari tautan Google Maps). Koreksi di Pengaturan Website bila perlu.
        db_exec('UPDATE villages SET maps_lat = ?, maps_lng = ? WHERE id = 1 AND maps_lat IS NULL', [-6.94936, 108.52098]);
    }

    private static function seedAccess(): void
    {
        $roles = [
            'super_admin' => ['Super Admin', 'Mengelola seluruh sistem desa'],
            'admin_desa'  => ['Admin Desa', 'Mengelola konten, pengguna sesuai izin, dan menyetujui publikasi'],
            'editor'      => ['Editor', 'Membuat dan mengedit konten, lalu mengajukan untuk persetujuan'],
            'operator'    => ['Operator', 'Mengelola modul yang diberikan kepadanya'],
            'viewer'      => ['Viewer', 'Hanya melihat dashboard'],
        ];
        $roleId = [];
        foreach ($roles as $slug => [$name, $desc]) {
            $roleId[$slug] = db_insert('roles', ['slug' => $slug, 'name' => $name, 'description' => $desc]);
        }

        $contentMods = ['pages', 'government', 'posts', 'announcements', 'events', 'potentials', 'documents', 'services', 'gallery'];
        $perms = [];
        foreach ($contentMods as $m) foreach (['view', 'create', 'edit', 'delete', 'publish'] as $a) $perms[] = "$m.$a";
        foreach (['complaints' => ['view', 'edit', 'delete'], 'users' => ['view', 'create', 'edit', 'delete'],
                  'statistics' => ['view', 'create', 'edit', 'delete'], 'categories' => ['view', 'create', 'edit', 'delete'],
                  'settings' => ['view', 'edit'], 'media' => ['view', 'create', 'delete'], 'audit' => ['view']] as $m => $acts) {
            foreach ($acts as $a) $perms[] = "$m.$a";
        }
        $permId = [];
        foreach ($perms as $p) $permId[$p] = db_insert('permissions', ['slug' => $p]);

        $grant = function (string $role, array $list) use ($roleId, $permId): void {
            foreach ($list as $p) {
                if (isset($permId[$p])) db_exec('INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES (?,?)', [$roleId[$role], $permId[$p]]);
            }
        };
        $grant('super_admin', $perms);
        $grant('admin_desa', array_values(array_filter($perms, function ($p) { return $p !== 'users.delete' && $p !== 'audit.view'; })));

        $ced = [];
        foreach ($contentMods as $m) foreach (['view', 'create', 'edit'] as $a) $ced[] = "$m.$a";
        $grant('editor', array_merge($ced, ['media.view', 'media.create', 'categories.view']));

        $ops = [];
        foreach (['documents', 'services', 'gallery', 'potentials', 'events'] as $m) foreach (['view', 'create', 'edit'] as $a) $ops[] = "$m.$a";
        $grant('operator', array_merge($ops, ['media.view', 'media.create']));

        $view = [];
        foreach ($contentMods as $m) $view[] = "$m.view";
        $grant('viewer', array_merge($view, ['complaints.view', 'statistics.view', 'media.view']));

        $now = now();
        db_insert('users', ['role_id' => $roleId['super_admin'], 'name' => 'Administrator', 'username' => 'admin',
            'email' => '', 'password_hash' => password_hash('admin12345', PASSWORD_DEFAULT), 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
        db_insert('users', ['role_id' => $roleId['editor'], 'name' => 'Editor Contoh', 'username' => 'editor',
            'email' => '', 'password_hash' => password_hash('editor12345', PASSWORD_DEFAULT), 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
    }

    private static function seedContent(): void
    {
        $now = now();
        $ago = function (int $d): string { return date('Y-m-d H:i:s', strtotime("-$d days")); };
        $demo = config('demo_marker');

        db_insert('villages', ['id' => 1, 'name' => 'Desa Widarasari', 'district' => 'Kramatmulya', 'regency' => 'Kuningan',
            'province' => 'Jawa Barat', 'motto' => 'Akur Makmur Ngawangun Lembur', 'is_verified' => 0, 'updated_at' => $now]);

        $settings = [
            'hero_title' => 'Selamat Datang di Desa Widarasari',
            'hero_subtitle' => 'Pusat informasi, layanan, dan potensi Desa Widarasari dalam satu platform digital.',
            'hero_image' => 'assets/img/hero/hero-desa.jpg',
            'sambutan_name' => "Nama Kepala Desa $demo",
            'sambutan_position' => 'Kepala Desa Widarasari',
            'sambutan_text' => "$demo Teks sambutan contoh. Ganti dengan sambutan resmi Kepala Desa melalui Pengaturan Website.",
            'sambutan_photo' => 'assets/img/people/kepala-desa.jpg',
            'footer_description' => 'WidaraHub adalah platform digital desa yang menyatukan informasi publik, potensi desa, transparansi, dan layanan masyarakat.',
            'platform_tagline' => 'One Village. One Digital Hub.',
            'org_chart_image' => 'assets/img/pages/struktur-organisasi.png',
        ];
        foreach ($settings as $k => $v) db_insert('site_settings', ['setting_key' => $k, 'setting_value' => $v]);

        $cats = [
            'post' => ['Pemerintahan', 'Pembangunan', 'Kegiatan Masyarakat', 'Pendidikan', 'Sosial', 'Kesehatan', 'Pengumuman'],
            'potential' => ['Wisata', 'UMKM', 'Kuliner', 'Pertanian', 'Perkebunan', 'Kerajinan', 'Produk Lokal'],
            'document' => ['APBDes', 'Realisasi Anggaran', 'Program Pembangunan', 'Dokumentasi Pembangunan', 'Laporan Kegiatan', 'Dokumen Publik Lainnya'],
            'gallery' => ['Kegiatan', 'Pembangunan', 'Wisata', 'Budaya', 'Masyarakat'],
        ];
        $cid = [];
        foreach ($cats as $type => $names) foreach ($names as $n) {
            $cid[$type][$n] = db_insert('categories', ['type' => $type, 'name' => $n, 'slug' => slugify($n), 'created_at' => $now, 'updated_at' => $now]);
        }

        $pub = function (int $days = 0) use ($ago, $now): array {
            return ['status' => 'published', 'published_at' => $ago($days), 'created_at' => $now, 'updated_at' => $now];
        };

        foreach ([['Statistik Penduduk', 'Jumlah Penduduk'], ['Statistik Penduduk', 'Kepala Keluarga'], ['Wilayah', 'Jumlah RT/RW'], ['Wilayah', 'Luas Wilayah']] as $i => [$g, $label]) {
            db_insert('village_statistics', ['label' => $label, 'value' => '—', 'unit' => '', 'sort_order' => $i, 'is_verified' => 1, 'created_at' => $now, 'updated_at' => $now]);
        }

        $profil = [
            ['sejarah-desa', 'Sejarah Desa'], ['asal-usul-nama-desa', 'Asal-usul Nama Desa'], ['visi-misi', 'Visi dan Misi'],
            ['kondisi-geografis', 'Kondisi Geografis'], ['batas-wilayah', 'Batas Wilayah'], ['demografi', 'Demografi'],
            ['sosial-budaya', 'Sosial Budaya'], ['pendidikan', 'Pendidikan'], ['mata-pencaharian', 'Mata Pencaharian'],
            ['sarana-prasarana', 'Sarana dan Prasarana'],
        ];
        foreach ($profil as $i => [$slug, $title]) {
            db_insert('pages', ['section' => 'profil', 'title' => $title, 'slug' => $slug,
                'body' => "<p>$demo Konten contoh. Ganti dengan informasi resmi Desa Widarasari yang telah diverifikasi dan disetujui.</p>",
                'sort_order' => $i] + $pub(10));
        }

        db_insert('pages', ['section' => 'umum', 'title' => 'Kebijakan Privasi', 'slug' => 'kebijakan-privasi',
            'body' => "<p>$demo Kebijakan privasi contoh. Ganti dengan kebijakan resmi yang mengacu pada peraturan perlindungan data pribadi dan keterbukaan informasi publik di Indonesia.</p>",
            'sort_order' => 99] + $pub(10));

        $posts = [['Contoh Berita: Kegiatan Gotong Royong Warga', 'Kegiatan Masyarakat'], ['Contoh Berita: Informasi Pembangunan Desa', 'Pembangunan'], ['Contoh Berita: Layanan Kesehatan Masyarakat', 'Kesehatan']];
        foreach ($posts as $i => [$t, $c]) {
            db_insert('posts', ['category_id' => $cid['post'][$c], 'title' => "$t $demo", 'slug' => 'contoh-berita-' . ($i + 1),
                'excerpt' => "$demo Ringkasan berita contoh untuk menguji tampilan kartu berita.",
                'body' => "<p>$demo Isi berita contoh. Hapus atau ganti dengan berita resmi dari dashboard.</p>",
                'thumbnail' => 'assets/img/news/berita-0' . ($i + 1) . '.jpg'] + $pub($i + 1));
        }

        db_insert('announcements', ['title' => "Contoh Pengumuman Penting $demo", 'slug' => 'contoh-pengumuman-1',
            'body' => "<p>$demo Isi pengumuman contoh.</p>", 'priority' => 1] + $pub(1));
        db_insert('announcements', ['title' => "Contoh Pengumuman Biasa $demo", 'slug' => 'contoh-pengumuman-2',
            'body' => "<p>$demo Isi pengumuman contoh.</p>", 'priority' => 0] + $pub(3));

        foreach ([3, 10, 21] as $i => $d) {
            db_insert('events', ['title' => 'Contoh Agenda Desa ' . ($i + 1) . " $demo", 'description' => "$demo Deskripsi agenda contoh.",
                'location' => 'Balai Desa', 'starts_at' => date('Y-m-d 09:00:00', strtotime("+$d days"))] + $pub(0));
        }

        $pot = [['Contoh Potensi Wisata', 'Wisata'], ['Contoh Produk UMKM', 'UMKM'], ['Contoh Hasil Pertanian', 'Pertanian']];
        foreach ($pot as $i => [$t, $c]) {
            db_insert('potentials', ['category_id' => $cid['potential'][$c], 'title' => "$t $demo", 'slug' => 'contoh-potensi-' . ($i + 1),
                'description' => "<p>$demo Deskripsi potensi contoh.</p>", 'location' => 'Desa Widarasari',
                'thumbnail' => 'assets/img/potential/potensi-0' . ($i + 1) . '.jpg'] + $pub($i + 2));
        }

        $gov = [['pemerintah_desa', "Nama Kepala Desa $demo", 'Kepala Desa'], ['pemerintah_desa', "Nama Sekretaris Desa $demo", 'Sekretaris Desa'],
                ['bpd', "Nama Ketua BPD $demo", 'Ketua BPD'], ['pkk', "Nama Ketua PKK $demo", 'Ketua PKK'], ['karang_taruna', "Nama Ketua $demo", 'Ketua Karang Taruna']];
        foreach ($gov as $i => [$u, $n, $p]) {
            db_insert('government_members', ['unit' => $u, 'name' => $n, 'position' => $p, 'description' => '',
                'photo' => 'assets/img/people/pejabat-0' . ($i + 1) . '.jpg', 'sort_order' => $i] + $pub(5));
        }

        foreach ([['Surat Keterangan Domisili', 'surat-keterangan-domisili'], ['Surat Keterangan Tidak Mampu', 'surat-keterangan-tidak-mampu'], ['Surat Pengantar', 'surat-pengantar']] as $i => [$t, $s]) {
            db_insert('services', ['title' => "$t $demo", 'slug' => $s, 'summary' => "$demo Ringkasan layanan contoh.",
                'requirements' => "$demo Persyaratan contoh — ganti dengan persyaratan resmi.", 'procedure' => "$demo Prosedur contoh — ganti dengan prosedur resmi.",
                'contact' => '', 'sort_order' => $i] + $pub(5));
        }

        db_insert('public_documents', ['category_id' => $cid['document']['APBDes'], 'title' => "Contoh Dokumen APBDes $demo",
            'description' => "$demo Dokumen contoh tanpa berkas.", 'year' => (int)date('Y')] + $pub(7));

        foreach (['Kegiatan', 'Pembangunan', 'Wisata', 'Budaya', 'Masyarakat', 'Kegiatan'] as $i => $c) {
            db_insert('galleries', ['category_id' => $cid['gallery'][$c], 'title' => "Contoh Foto " . ($i + 1) . " $demo", 'description' => '',
                'media_type' => 'foto', 'image' => 'assets/img/gallery/galeri-0' . ($i + 1) . '.jpg', 'album' => 'Album Contoh'] + $pub($i));
        }
    }
}
