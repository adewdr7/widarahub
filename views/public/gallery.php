<?php /* Variabel: $items, $pg, $cat, $categories */ ?>
<section class="container section">
  <h1>Galeri Desa</h1>
  <?php partial('filter_bar', ['categories' => $categories, 'cat' => $cat, 'base' => '/galeri']); ?>
  <div class="gallery-grid">
    <?php foreach ($items as $g): $src = media_url($g['image']); ?>
      <?php if ($g['media_type'] === 'video' && $g['video_url']): ?>
        <a class="gallery-item gallery-item--video" href="<?= e($g['video_url']) ?>" target="_blank" rel="noopener">
          <img src="<?= e($src) ?>" alt="<?= e($g['title']) ?>" width="1600" height="1067" loading="lazy"><span>▶ <?= e($g['title']) ?></span>
        </a>
      <?php else: ?>
        <a class="gallery-item" href="<?= e($src) ?>" data-lightbox data-caption="<?= e($g['title']) ?>">
          <img src="<?= e($src) ?>" alt="<?= e($g['title']) ?>" width="1600" height="1067" loading="lazy"><span><?= e($g['title']) ?></span>
        </a>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
  <?php if (!$items): ?><p class="muted">Belum ada foto atau video.</p><?php endif; ?>
  <?php partial('pagination', ['pg' => $pg]); ?>
</section>
