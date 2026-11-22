<?php
// Konfigurasi utama WidaraHub
return [
    'app_name'  => 'WidaraHub',
    'debug'     => true,                 // set false saat dipakai publik
    'timezone'  => 'Asia/Jakarta',
    'db_path'   => BASE_PATH . '/storage/widarahub.sqlite',
    'per_page'  => 9,

    // Upload (penyimpanan LOKAL di public/uploads — belum memakai layanan cloud)
    'upload_max_bytes' => 5 * 1024 * 1024,
    'upload_image_ext' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
    'upload_doc_ext'   => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],

    // Navigasi utama publik
    'nav' => [
        ['Beranda', '/'],
        ['Profil Desa', '/profil'],
        ['Pemerintahan', '/pemerintahan'],
        ['Informasi', '/informasi'],
        ['Potensi Desa', '/potensi'],
        ['Transparansi', '/transparansi'],
        ['Keuangan', '/keuangan'],
        ['Layanan', '/layanan'],
        ['Kontak', '/kontak'],
    ],

    'gov_units' => [
        'pemerintah_desa' => 'Pemerintah Desa',
        'bpd'             => 'BPD',
        'rt_rw'           => 'RT/RW',
        'pkk'             => 'PKK',
        'karang_taruna'   => 'Karang Taruna',
        'lembaga'         => 'Lembaga Desa',
    ],

    'status_labels' => [
        'draft' => 'Draft', 'submitted' => 'Diajukan', 'review' => 'Dalam Review',
        'approved' => 'Disetujui', 'published' => 'Terpublikasi',
        'rejected' => 'Ditolak', 'archived' => 'Arsip',
    ],

    'complaint_status' => [
        'baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak',
    ],

    // Modul Keuangan & Tujuan Desa
    'finance_kinds' => ['pendapatan' => 'Pendapatan', 'belanja' => 'Belanja', 'pembiayaan' => 'Pembiayaan'],
    'goal_pillars' => [
        'ekonomi' => 'Ekonomi Desa', 'sdm' => 'SDM Berkualitas', 'pendidikan' => 'Pendidikan Bermutu',
        'infrastruktur' => 'Infrastruktur & Fasilitas', 'layanan' => 'Keragaman Layanan',
    ],
    'need_categories' => [
        'usaha_jasa' => 'Usaha & Jasa', 'layanan' => 'Layanan Publik', 'fasilitas' => 'Fasilitas & Infrastruktur', 'pelatihan' => 'Pelatihan & SDM',
    ],
    'need_status' => ['dibutuhkan' => 'Dibutuhkan', 'dirintis' => 'Sedang dirintis', 'tersedia' => 'Sudah tersedia'],

    // Penanda konten contoh. Konten yang mengandung teks ini dianggap BELUM resmi.
    'demo_marker' => '[DEMO]',
];
