<?php /* Variabel: $items, $pg, $cat, $categories */ ?>
<section class="container section">
  <h1>Transparansi & Dokumen Publik</h1>
  <p class="muted">Hanya dokumen yang telah diverifikasi dan disetujui yang dipublikasikan.</p>
  <?php partial('filter_bar', ['categories' => $categories, 'cat' => $cat, 'base' => '/transparansi']); ?>
  <?php foreach ($items as $d): $file = $d['file_path'] && is_file(BASE_PATH . '/public/' . $d['file_path']); ?>
    <article class="list-item doc">
      <div>
        <?php if ($d['category_name']): ?><span class="tag"><?= e($d['category_name']) ?></span><?php endif; ?>
        <h3><?= e($d['title']) ?><?= $d['year'] ? ' (' . (int)$d['year'] . ')' : '' ?></h3>
        <time class="muted"><?= e(id_date($d['published_at'])) ?></time>
        <?php if ($d['description']): ?><p><?= e($d['description']) ?></p><?php endif; ?>
      </div>
      <?php if ($file): ?>
        <div class="doc__actions">
          <a class="btn btn--small" href="/<?= e($d['file_path']) ?>" target="_blank" rel="noopener">Lihat</a>
          <a class="btn btn--small btn--light" href="/<?= e($d['file_path']) ?>" download>Unduh</a>
        </div>
      <?php endif; ?>
    </article>
  <?php endforeach; ?>
  <?php if (!$items): ?><p class="muted">Belum ada dokumen terpublikasi.</p><?php endif; ?>
  <?php partial('pagination', ['pg' => $pg]); ?>
</section>
