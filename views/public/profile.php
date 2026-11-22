<?php /* Variabel: $pages */ ?>
<section class="container section">
  <h1>Profil Desa</h1>
  <div class="grid grid--3">
    <?php foreach ($pages as $p): ?>
      <a class="card card--link" href="/profil/<?= e($p['slug']) ?>"><div class="card__body"><h3><?= e($p['title']) ?></h3><p class="muted"><?= e(excerpt($p['body'], 90)) ?></p></div></a>
    <?php endforeach; ?>
  </div>
</section>
