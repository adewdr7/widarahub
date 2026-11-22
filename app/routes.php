<?php
/** @var Router $router */
$r = $router;

// ---------- Publik ----------
$r->get('/', [PublicController::class, 'home']);
$r->get('/profil', [PublicController::class, 'profile']);
$r->get('/profil/{slug}', [PublicController::class, 'page']);
$r->get('/halaman/{slug}', [PublicController::class, 'page']);
$r->get('/pemerintahan', [PublicController::class, 'government']);
$r->get('/informasi', [PublicController::class, 'posts']);
$r->get('/informasi/{slug}', [PublicController::class, 'post']);
$r->get('/pengumuman', [PublicController::class, 'announcements']);
$r->get('/agenda', [PublicController::class, 'events']);
$r->get('/potensi', [PublicController::class, 'potentials']);
$r->get('/potensi/{slug}', [PublicController::class, 'potential']);
$r->get('/transparansi', [PublicController::class, 'documents']);
$r->get('/keuangan', [PublicController::class, 'finance']);
$r->get('/pelatihan', [PublicController::class, 'trainings']);
$r->post('/minat', [PublicController::class, 'interestSubmit']);
$r->get('/layanan', [PublicController::class, 'services']);
$r->get('/galeri', [PublicController::class, 'gallery']);
$r->get('/kontak', [PublicController::class, 'contact']);
$r->post('/kontak', [PublicController::class, 'contactSubmit']);
$r->get('/cari', [PublicController::class, 'search']);
$r->get('/sitemap.xml', [PublicController::class, 'sitemap']);
$r->get('/robots.txt', [PublicController::class, 'robots']);

// ---------- Admin ----------
$r->get('/admin/login', [AuthController::class, 'loginForm']);
$r->post('/admin/login', [AuthController::class, 'login']);
$r->post('/admin/logout', [AuthController::class, 'logout']);
$r->get('/admin', [AdminController::class, 'dashboard']);
$r->get('/admin/audit', [AdminController::class, 'audit']);

$r->get('/admin/media', [MediaController::class, 'index']);
$r->post('/admin/media/upload', [MediaController::class, 'upload']);
$r->post('/admin/media/{id}/replace', [MediaController::class, 'replace']);
$r->post('/admin/media/{id}/delete', [MediaController::class, 'destroy']);

$r->get('/admin/roles', [RoleController::class, 'index']);
$r->get('/admin/roles/create', [RoleController::class, 'create']);
$r->post('/admin/roles', [RoleController::class, 'store']);
$r->get('/admin/roles/{id}/edit', [RoleController::class, 'edit']);
$r->post('/admin/roles/{id}', [RoleController::class, 'update']);
$r->post('/admin/roles/{id}/delete', [RoleController::class, 'destroy']);

$r->get('/admin/settings', [SettingsController::class, 'index']);
$r->post('/admin/settings', [SettingsController::class, 'save']);

$r->get('/admin/r/{res}', [ResourceController::class, 'index']);
$r->get('/admin/r/{res}/create', [ResourceController::class, 'create']);
$r->post('/admin/r/{res}', [ResourceController::class, 'store']);
$r->get('/admin/r/{res}/{id}/edit', [ResourceController::class, 'edit']);
$r->post('/admin/r/{res}/{id}', [ResourceController::class, 'update']);
$r->post('/admin/r/{res}/{id}/delete', [ResourceController::class, 'destroy']);
$r->post('/admin/r/{res}/{id}/restore/{rev}', [ResourceController::class, 'restore']);
