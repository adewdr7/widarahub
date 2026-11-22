<?php /* Variabel: $res, $item, $values, $errors, $fields, $revisions */
$base = '/admin/r/' . $res['key'];
$action = $item ? $base . '/' . $item['id'] : $base;
?>
<div class="page-head"><h1><?= e($title) ?></h1><a class="btn btn--light" href="<?= e($base) ?>">← Kembali</a></div>
<?php if ($errors): ?><div class="flash flash--error">Periksa kembali isian yang ditandai.</div><?php endif; ?>
<?php if ($item && ($item['status'] ?? '') === 'rejected' && !empty($item['review_note'])): ?>
  <div class="flash flash--error"><strong>Catatan revisi:</strong> <?= e($item['review_note']) ?></div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="form form--admin">
  <?= csrf_field() ?>
  <?php require BASE_PATH . '/views/admin/_fields.php'; ?>
  <div class="form-actions">
    <?php if (empty($res['no_create']) || $item): ?><button class="btn" type="submit">Simpan</button><?php endif; ?>
    <a class="btn btn--light" href="<?= e($base) ?>">Batal</a>
  </div>
</form>

<?php if ($revisions): ?>
<section class="panel">
  <h2>Riwayat revisi</h2>
  <?php foreach ($revisions as $r): ?>
    <form method="post" action="<?= e($base . '/' . $item['id'] . '/restore/' . $r['id']) ?>" data-confirm="Pulihkan revisi ini sebagai draft?" class="inline-form">
      <?= csrf_field() ?>
      <span><?= e(id_date($r['created_at'], true)) ?> · <?= e($r['username'] ?? '-') ?></span>
      <button class="btn btn--small btn--light" type="submit">Pulihkan</button>
    </form>
  <?php endforeach; ?>
</section>
<?php endif; ?>
