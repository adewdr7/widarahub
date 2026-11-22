<?php /* Variabel: $p (baris posts + category_name) */ ?>
<article class="card post-card">
  <a class="card__img" href="/informasi/<?= e($p['slug']) ?>">
    <img src="<?= e(media_url($p['thumbnail'])) ?>" alt="" width="1200" height="675" loading="lazy">
  </a>
  <div class="card__body">
    <?php if (!empty($p['category_name'])): ?><span class="tag"><?= e($p['category_name']) ?></span><?php endif; ?>
    <h3><a href="/informasi/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
    <time class="muted" datetime="<?= e(substr((string)$p['published_at'], 0, 10)) ?>"><?= e(id_date($p['published_at'])) ?></time>
    <p><?= e($p['excerpt'] ?: excerpt($p['body'], 120)) ?></p>
    <a class="link-more" href="/informasi/<?= e($p['slug']) ?>">Baca selengkapnya →</a>
  </div>
</article>
