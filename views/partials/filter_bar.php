<?php /* Variabel: $categories (baris categories), $cat (slug aktif), $base (path), $q (opsional) */ ?>
<nav class="filter-bar" aria-label="Filter kategori">
  <a href="<?= e($base) ?>"<?= $cat === '' ? ' aria-current="true"' : '' ?>>Semua</a>
  <?php foreach ($categories as $c): ?>
    <a href="<?= e($base . '?kategori=' . urlencode($c['slug'])) ?>"<?= $cat === $c['slug'] ? ' aria-current="true"' : '' ?>><?= e($c['name']) ?></a>
  <?php endforeach; ?>
</nav>
