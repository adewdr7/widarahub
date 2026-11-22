<?php /* Variabel: $pg (hasil paginate()) */ if ($pg['pages'] > 1): ?>
<nav class="pagination" aria-label="Halaman">
  <?php if ($pg['page'] > 1): ?><a href="<?= e(page_url($pg['page'] - 1)) ?>" rel="prev">‹ Sebelumnya</a><?php endif; ?>
  <span>Halaman <?= (int)$pg['page'] ?> dari <?= (int)$pg['pages'] ?></span>
  <?php if ($pg['page'] < $pg['pages']): ?><a href="<?= e(page_url($pg['page'] + 1)) ?>" rel="next">Berikutnya ›</a><?php endif; ?>
</nav>
<?php endif; ?>
