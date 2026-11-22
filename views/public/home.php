<?php
/* Variabel: $posts, $potentials, $announcements, $events, $stats, $quick */
$v = village();
$heroImg = media_url(setting('hero_image', 'assets/img/hero/hero-desa.jpg'));
$sambutanPhoto = media_url(setting('sambutan_photo', ''), 'assets/img/placeholder-orang.svg');
?>
<section class="hero">
  <img class="hero__img" src="<?= e($heroImg) ?>" alt="Pemandangan <?= e($v['name']) ?>" width="1920" height="1080" fetchpriority="high">
  <div class="container hero__content">
    <h1><?= e(setting('hero_title', 'Selamat Datang di ' . $v['name'])) ?></h1>
    <p><?= e(setting('hero_subtitle')) ?></p>
    <div class="hero__cta">
      <a class="btn" href="/profil">Jelajahi Desa</a>
      <a class="btn btn--light" href="/layanan">Informasi Pelayanan</a>
    </div>
  </div>
</section>

<section class="container section quick-access" aria-label="Akses cepat">
  <?php foreach ($quick as [$label, $href, $icon]): ?>
    <a class="quick-item" href="<?= e($href) ?>">
      <img src="<?= e(media_url($icon, 'assets/img/placeholder.svg')) ?>" alt="" width="64" height="64" loading="lazy">
      <span><?= e($label) ?></span>
    </a>
  <?php endforeach; ?>
</section>

<section class="container section sambutan">
  <img src="<?= e($sambutanPhoto) ?>" alt="<?= e(setting('sambutan_name')) ?>" width="600" height="750" loading="lazy">
  <div>
    <h2>Sambutan Kepala Desa</h2>
    <p><?= nl2br(e(setting('sambutan_text'))) ?></p>
    <p><strong><?= e(setting('sambutan_name')) ?></strong><br><span class="muted"><?= e(setting('sambutan_position')) ?></span></p>
    <a class="link-more" href="/pemerintahan">Profil lengkap →</a>
  </div>
</section>

<section class="container section">
  <div class="section__head"><h2>Berita Terbaru</h2><a class="link-more" href="/informasi">Semua berita →</a></div>
  <?php if ($posts): ?>
    <div class="grid grid--3"><?php foreach ($posts as $p) partial('post_card', ['p' => $p]); ?></div>
  <?php else: ?><p class="muted">Belum ada berita terpublikasi.</p><?php endif; ?>
</section>

<?php if ($stats): ?>
<section class="section section--tint"><div class="container">
  <h2>Statistik Desa</h2>
  <div class="grid grid--4">
    <?php foreach ($stats as $s): ?>
      <div class="stat"><strong><?= e($s['value']) ?></strong><span><?= e($s['label']) ?><?= $s['unit'] ? ' (' . e($s['unit']) . ')' : '' ?></span></div>
    <?php endforeach; ?>
  </div>
</div></section>
<?php endif; ?>

<section class="container section">
  <div class="section__head"><h2>Potensi Desa</h2><a class="link-more" href="/potensi">Semua potensi →</a></div>
  <?php if ($potentials): ?>
    <div class="grid grid--3">
      <?php foreach ($potentials as $p): ?>
        <article class="card">
          <a class="card__img" href="/potensi/<?= e($p['slug']) ?>"><img src="<?= e(media_url($p['thumbnail'])) ?>" alt="" width="1200" height="800" loading="lazy"></a>
          <div class="card__body">
            <?php if ($p['category_name']): ?><span class="tag"><?= e($p['category_name']) ?></span><?php endif; ?>
            <h3><a href="/potensi/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php else: ?><p class="muted">Belum ada potensi terpublikasi.</p><?php endif; ?>
</section>

<section class="container section two-col">
  <div>
    <div class="section__head"><h2>Pengumuman</h2><a class="link-more" href="/pengumuman">Semua →</a></div>
    <?php foreach ($announcements as $a): ?>
      <article class="list-item<?= $a['priority'] ? ' list-item--important' : '' ?>">
        <h3><?= e($a['title']) ?></h3>
        <time class="muted"><?= e(id_date($a['published_at'])) ?></time>
      </article>
    <?php endforeach; ?>
    <?php if (!$announcements): ?><p class="muted">Tidak ada pengumuman aktif.</p><?php endif; ?>
  </div>
  <div>
    <div class="section__head"><h2>Agenda Desa</h2><a class="link-more" href="/agenda">Semua →</a></div>
    <?php foreach ($events as $ev): ?>
      <article class="list-item">
        <h3><?= e($ev['title']) ?></h3>
        <time class="muted"><?= e(id_date($ev['starts_at'], true)) ?></time>
        <?php if ($ev['location']): ?><span class="muted"> · <?= e($ev['location']) ?></span><?php endif; ?>
      </article>
    <?php endforeach; ?>
    <?php if (!$events): ?><p class="muted">Belum ada agenda mendatang.</p><?php endif; ?>
  </div>
</section>
