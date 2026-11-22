<?php /* Variabel: $posts, $pg, $q, $cat, $categories */ ?>
<section class="container section">
  <h1>Informasi & Berita</h1>
  <form class="search-form" method="get" action="/informasi" role="search">
    <label class="sr-only" for="q">Cari berita</label>
    <input id="q" type="search" name="q" value="<?= e($q) ?>" placeholder="Cari berita…">
    <?php if ($cat !== ''): ?><input type="hidden" name="kategori" value="<?= e($cat) ?>"><?php endif; ?>
    <button class="btn" type="submit">Cari</button>
  </form>
  <?php partial('filter_bar', ['categories' => $categories, 'cat' => $cat, 'base' => '/informasi']); ?>
  <?php if ($posts): ?>
    <div class="grid grid--3"><?php foreach ($posts as $p) partial('post_card', ['p' => $p]); ?></div>
  <?php else: ?><p class="muted">Tidak ada berita yang cocok.</p><?php endif; ?>
  <?php partial('pagination', ['pg' => $pg]); ?>
</section>
