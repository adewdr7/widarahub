<?php /* Variabel: $groups  [unit => ['label'=>..., 'members'=>[...]]] */
$chart = setting('org_chart_image', '');
?>
<section class="container section">
  <h1>Pemerintahan Desa</h1>
  <?php if ($chart && is_file(BASE_PATH . '/public/' . ltrim($chart, '/'))): ?>
    <figure class="org-chart"><img src="<?= e(media_url($chart)) ?>" alt="Bagan struktur organisasi" width="1600" height="1000" loading="lazy"><figcaption>Struktur organisasi</figcaption></figure>
  <?php endif; ?>
  <?php foreach ($groups as $g): ?>
    <h2><?= e($g['label']) ?></h2>
    <div class="grid grid--4">
      <?php foreach ($g['members'] as $m): ?>
        <article class="card person">
          <img src="<?= e(media_url($m['photo'], 'assets/img/placeholder-orang.svg')) ?>" alt="<?= e($m['name']) ?>" width="400" height="500" loading="lazy">
          <div class="card__body">
            <h3><?= e($m['name']) ?></h3>
            <p class="muted"><?= e($m['position']) ?></p>
            <?php if ($m['description']): ?><p><?= e($m['description']) ?></p><?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
  <?php if (!$groups): ?><p class="muted">Data pemerintahan belum dipublikasikan.</p><?php endif; ?>
</section>
