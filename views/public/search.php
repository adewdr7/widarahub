<?php /* Variabel: $q, $results */ ?>
<section class="container section">
  <h1>Pencarian</h1>
  <form class="search-form" method="get" action="/cari" role="search">
    <label class="sr-only" for="q">Kata kunci</label>
    <input id="q" type="search" name="q" value="<?= e($q) ?>" placeholder="Cari berita, potensi, halaman…" autofocus>
    <button class="btn" type="submit">Cari</button>
  </form>
  <?php if ($q !== ''): ?>
    <p class="muted"><?= count($results) ?> hasil untuk “<?= e($q) ?>”</p>
    <?php foreach ($results as $r): ?>
      <article class="list-item"><span class="tag"><?= e($r['type']) ?></span>
        <h3><a href="<?= e($r['url']) ?>"><?= e($r['title']) ?></a></h3><p><?= e($r['summary']) ?></p></article>
    <?php endforeach; ?>
  <?php endif; ?>
</section>
