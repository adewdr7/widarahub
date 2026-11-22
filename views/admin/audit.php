<?php /* Variabel: $rows, $pg, $q */ ?>
<div class="page-head"><h1>Audit Log</h1></div>
<form class="toolbar" method="get" action="/admin/audit">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari pengguna / aksi / tabel…">
  <button class="btn btn--light" type="submit">Cari</button>
</form>
<div class="table-wrap"><table class="table">
  <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Tabel</th><th>ID</th><th>Detail</th><th>IP</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr><td><?= e(id_date($r['created_at'], true)) ?></td><td><?= e($r['username']) ?></td><td><?= e($r['action']) ?></td>
      <td><?= e($r['table_name']) ?></td><td><?= e($r['record_id']) ?></td><td><?= e(excerpt($r['detail'], 60)) ?></td><td><?= e($r['ip']) ?></td></tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="7" class="muted">Belum ada catatan.</td></tr><?php endif; ?>
  </tbody>
</table></div>
<?php partial('pagination', ['pg' => $pg]); ?>
