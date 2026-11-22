<?php /* Variabel: $upcoming, $past */ ?>
<section class="container section">
  <h1>Agenda Desa</h1>
  <h2>Akan datang</h2>
  <?php foreach ($upcoming as $ev): ?>
    <article class="list-item"><h3><?= e($ev['title']) ?></h3>
      <time class="muted"><?= e(id_date($ev['starts_at'], true)) ?></time><?= $ev['location'] ? ' · <span class="muted">' . e($ev['location']) . '</span>' : '' ?>
      <?php if ($ev['description']): ?><p><?= e($ev['description']) ?></p><?php endif; ?></article>
  <?php endforeach; ?>
  <?php if (!$upcoming): ?><p class="muted">Belum ada agenda mendatang.</p><?php endif; ?>
  <?php if ($past): ?><h2>Telah berlalu</h2>
    <?php foreach ($past as $ev): ?><article class="list-item"><h3><?= e($ev['title']) ?></h3><time class="muted"><?= e(id_date($ev['starts_at'])) ?></time></article><?php endforeach; ?>
  <?php endif; ?>
</section>
