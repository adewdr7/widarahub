<?php /* Variabel: $page, $siblings */ ?>
<?php if (!empty($page['thumbnail'])): ?>
  <div class="page-banner"><img src="<?= e(media_url($page['thumbnail'])) ?>" alt="" width="1600" height="400"></div>
<?php endif; ?>
<section class="container section page-layout">
  <article class="prose">
    <h1><?= e($page['title']) ?></h1>
    <?= clean_html($page['body']) ?>
  </article>
  <?php if ($siblings): ?>
    <aside class="side-nav" aria-label="Halaman profil">
      <h2>Profil Desa</h2>
      <?php foreach ($siblings as $s): ?>
        <a href="/profil/<?= e($s['slug']) ?>"<?= $s['slug'] === $page['slug'] ? ' aria-current="page"' : '' ?>><?= e($s['title']) ?></a>
      <?php endforeach; ?>
    </aside>
  <?php endif; ?>
</section>
