<?php /* Variabel: $items, $pg */ ?>
<section class="container section">
  <h1>Pengumuman</h1>
  <?php foreach ($items as $a): ?>
    <article class="list-item<?= $a['priority'] ? ' list-item--important' : '' ?>">
      <h2><?= e($a['title']) ?></h2>
      <time class="muted"><?= e(id_date($a['published_at'], true)) ?></time>
      <div class="prose"><?= clean_html($a['body']) ?></div>
    </article>
  <?php endforeach; ?>
  <?php if (!$items): ?><p class="muted">Belum ada pengumuman.</p><?php endif; ?>
  <?php partial('pagination', ['pg' => $pg]); ?>
</section>
