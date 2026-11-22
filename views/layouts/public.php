<?php
/* Layout publik. Variabel dari controller: $title, $meta_description (opsional), $og_image (opsional), $content */
$v = village();
$siteName = config('app_name');
$villageName = $v['name'] ?? 'Desa';
$pageTitle = !empty($title) ? $title . ' — ' . $villageName : $siteName . ' — ' . $villageName;
$metaDesc = !empty($meta_description) ? $meta_description : setting('footer_description', 'Platform digital desa.');
$ogImage = base_url() . (!empty($og_image) ? $og_image : media_url('assets/img/brand/og-default.jpg'));
$path = current_path();
$isActive = function (string $p) use ($path): bool { return $p === '/' ? $path === '/' : strpos($path, $p) === 0; };
$topAnn = db_one('SELECT title FROM announcements WHERE ' . pub_where() . ' AND priority = 1 AND (expires_at IS NULL OR expires_at >= ?) ORDER BY published_at DESC LIMIT 1', [date('Y-m-d')]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($metaDesc) ?>">
  <link rel="canonical" href="<?= e(base_url() . $path) ?>">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= e($pageTitle) ?>">
  <meta property="og:description" content="<?= e($metaDesc) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= e(media_url('assets/img/brand/favicon-32.png')) ?>">
  <link rel="apple-touch-icon" href="<?= e(media_url('assets/img/brand/favicon-180.png')) ?>">
  <link rel="stylesheet" href="<?= asset('css/base.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/theme.css') ?>">
</head>
<body>
<a class="skip-link" href="#main">Lewati ke konten utama</a>

<?php if (empty($v['is_verified'])): ?>
  <div class="demo-banner" role="note">Situs ini masih berisi <strong>konten contoh (demo)</strong>. Hanya informasi yang telah diverifikasi pemerintah desa yang bersifat resmi.</div>
<?php endif; ?>
<?php if ($topAnn): ?>
  <div class="alert-bar"><a href="/pengumuman">📢 <?= e($topAnn['title']) ?></a></div>
<?php endif; ?>

<header class="site-header">
  <div class="container site-header__inner">
    <a class="brand" href="/" aria-label="Beranda <?= e($siteName) ?>">
      <img src="<?= e(media_url('assets/img/brand/logo-widarahub-putih.svg', 'assets/img/brand/logo-icon.png')) ?>" alt="<?= e($siteName) ?>" width="160" height="40">
      <span class="brand__village"><?= e($villageName) ?></span>
    </a>
    <nav id="nav" class="main-nav" aria-label="Navigasi utama">
      <?php foreach (config('nav') as [$label, $href]): ?>
        <a href="<?= e($href) ?>"<?= $isActive($href) ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="header-tools">
      <a class="icon-btn" href="/cari" aria-label="Cari">🔍</a>
      <button class="icon-btn" type="button" data-a11y="font-down" aria-label="Perkecil teks">A−</button>
      <button class="icon-btn" type="button" data-a11y="font-up" aria-label="Perbesar teks">A+</button>
      <button class="icon-btn" type="button" data-a11y="contrast" aria-label="Kontras tinggi">◐</button>
      <button class="icon-btn nav-toggle" id="navToggle" type="button" aria-expanded="false" aria-controls="nav" aria-label="Menu">☰</button>
    </div>
  </div>
</header>

<?php foreach (flashes() as $f): ?>
  <div class="container"><div class="flash flash--<?= e($f['type']) ?>" role="status"><?= e($f['msg']) ?></div></div>
<?php endforeach; ?>

<main id="main"><?= $content ?></main>

<footer class="site-footer">
  <div class="container footer-grid">
    <div>
      <img src="<?= e(media_url('assets/img/brand/logo-widarahub-putih.svg', 'assets/img/brand/logo-icon.png')) ?>" alt="<?= e($siteName) ?>" width="160" height="40">
      <p><?= e(setting('footer_description')) ?></p>
      <p class="muted"><?= e($v['motto'] ?? '') ?></p>
      <p class="muted"><?= e(setting('platform_tagline')) ?></p>
    </div>
    <div>
      <h2>Kontak <?= e($villageName) ?></h2>
      <p><?= $v['address'] ? nl2br(e($v['address'])) : 'Alamat kantor belum diisi.' ?></p>
      <?php if ($v['phone']): ?><p>Telp: <?= e($v['phone']) ?></p><?php endif; ?>
      <?php if ($v['email']): ?><p>Email: <?= e($v['email']) ?></p><?php endif; ?>
      <?php if ($v['office_hours']): ?><p>Jam pelayanan:<br><?= nl2br(e($v['office_hours'])) ?></p><?php endif; ?>
    </div>
    <div>
      <h2>Navigasi cepat</h2>
      <ul class="footer-links">
        <?php foreach (config('nav') as [$label, $href]): ?><li><a href="<?= e($href) ?>"><?= e($label) ?></a></li><?php endforeach; ?>
        <li><a href="/pengumuman">Pengumuman</a></li><li><a href="/agenda">Agenda</a></li><li><a href="/galeri">Galeri</a></li>
      </ul>
      <h2>Media sosial</h2>
      <p>
        <?php foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube'] as $k => $lab): ?>
          <?php if (!empty($v[$k])): ?><a href="<?= e($v[$k]) ?>" rel="noopener" target="_blank"><?= $lab ?></a> <?php endif; ?>
        <?php endforeach; ?>
      </p>
    </div>
  </div>
  <div class="container footer-bottom">
    <span>© <?= date('Y') ?> <?= e($villageName) ?> · <?= e($siteName) ?></span>
    <a href="/halaman/kebijakan-privasi">Kebijakan Privasi</a>
  </div>
</footer>
<script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
