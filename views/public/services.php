<?php /* Variabel: $items */ $v = village(); ?>
<section class="container section">
  <h1>Layanan Publik</h1>
  <?php if ($v['office_hours']): ?><p><strong>Jam pelayanan:</strong> <?= nl2br(e($v['office_hours'])) ?></p><?php endif; ?>
  <?php foreach ($items as $s): $form = $s['form_file'] && is_file(BASE_PATH . '/public/' . $s['form_file']); ?>
    <details class="service">
      <summary><?= e($s['title']) ?></summary>
      <?php if ($s['summary']): ?><p><?= e($s['summary']) ?></p><?php endif; ?>
      <?php if ($s['requirements']): ?><h3>Persyaratan</h3><div class="prose"><?= clean_html($s['requirements']) ?></div><?php endif; ?>
      <?php if ($s['procedure']): ?><h3>Prosedur</h3><div class="prose"><?= clean_html($s['procedure']) ?></div><?php endif; ?>
      <?php if ($form): ?><p><a class="btn btn--small" href="/<?= e($s['form_file']) ?>" download>Unduh formulir</a></p><?php endif; ?>
      <?php if ($s['contact']): ?><p class="muted">Kontak petugas: <?= e($s['contact']) ?></p><?php endif; ?>
    </details>
  <?php endforeach; ?>
  <?php if (!$items): ?><p class="muted">Informasi layanan belum dipublikasikan.</p><?php endif; ?>
</section>
