<?php /* Variabel: $items, $pg, $cat, $categories */ ?>
<section class="container section">
  <h1>Potensi Desa</h1>
  <?php partial('filter_bar', ['categories' => $categories, 'cat' => $cat, 'base' => '/potensi']); ?>
  <?php if (!empty($markers)) partial('map', ['markers' => $markers, 'label' => 'Peta potensi desa']); ?>
  <div class="grid grid--3">
    <?php foreach ($items as $p): ?>
      <article class="card">
        <a class="card__img" href="/potensi/<?= e($p['slug']) ?>"><img src="<?= e(media_url($p['thumbnail'])) ?>" alt="" width="1200" height="800" loading="lazy"></a>
        <div class="card__body">
          <?php if ($p['category_name']): ?><span class="tag"><?= e($p['category_name']) ?></span><?php endif; ?>
          <h3><a href="/potensi/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
          <p><?= e(excerpt($p['description'], 110)) ?></p>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
  <?php if (!$items): ?><p class="muted">Belum ada potensi terpublikasi.</p><?php endif; ?>
  <?php partial('pagination', ['pg' => $pg]); ?>
</section>
