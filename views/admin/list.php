<?php /* Variabel: $res, $rows, $pg, $q, $status */
$base = '/admin/r/' . $res['key'];
$canEdit = Auth::can($res['perm'] . '.edit');
$canDel = Auth::can($res['perm'] . '.delete');
?>
<div class="page-head">
  <h1><?= e($res['label']) ?></h1>
  <?php if (Auth::can($res['perm'] . '.create') && empty($res['no_create'])): ?><a class="btn" href="<?= e($base) ?>/create">+ Tambah</a><?php endif; ?>
</div>

<form class="toolbar" method="get" action="<?= e($base) ?>">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari…">
  <?php if (!empty($res['workflow'])): ?>
    <select name="status">
      <option value="">Semua status</option>
      <?php foreach (config('status_labels') as $k => $lab): ?><option value="<?= e($k) ?>"<?= $status === $k ? ' selected' : '' ?>><?= e($lab) ?></option><?php endforeach; ?>
    </select>
  <?php endif; ?>
  <button class="btn btn--light" type="submit">Filter</button>
</form>

<div class="table-wrap">
<table class="table">
  <thead><tr>
    <?php foreach ($res['list'] as $lab): ?><th><?= e($lab) ?></th><?php endforeach; ?><th>Aksi</th>
  </tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <?php foreach ($r['cells'] as $col => $txt): ?>
        <td data-label="<?= e($res['list'][$col]) ?>">
          <?php if ($col === 'status' && $r['status']): ?><span class="badge badge--<?= e($r['status']) ?>"><?= e($txt) ?></span>
            <?php if ($r['status'] === 'rejected' && $r['note']): ?><br><small class="muted">Catatan: <?= e(excerpt($r['note'], 80)) ?></small><?php endif; ?>
          <?php else: ?><?= e($txt) ?><?php endif; ?>
        </td>
      <?php endforeach; ?>
      <td class="actions">
        <?php if ($canEdit): ?><a class="btn btn--small btn--light" href="<?= e($base . '/' . $r['id']) ?>/edit">Edit</a><?php endif; ?>
        <?php if ($canDel): ?>
          <form method="post" action="<?= e($base . '/' . $r['id']) ?>/delete" data-confirm="Hapus data ini?">
            <?= csrf_field() ?><button class="btn btn--small btn--danger" type="submit">Hapus</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="<?= count($res['list']) + 1 ?>" class="muted">Belum ada data.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>
<?php partial('pagination', ['pg' => $pg]); ?>
