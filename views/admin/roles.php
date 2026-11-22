<?php /* Variabel: $roles, $system, $total */ ?>
<div class="page-head">
  <h1>Peran & Izin</h1>
  <?php if (Auth::can('roles.create')): ?><a class="btn" href="/admin/roles/create">+ Peran baru</a><?php endif; ?>
</div>
<p class="muted">Peran menentukan menu dan aksi yang boleh dipakai pengguna. Perubahan izin langsung berlaku. Peran Super Admin tidak dapat diubah.</p>
<div class="table-wrap"><table class="table">
  <thead><tr><th>Peran</th><th>Deskripsi</th><th>Pengguna</th><th>Izin</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($roles as $r): $isSys = in_array($r['slug'], $system, true); ?>
    <tr>
      <td data-label="Peran"><strong><?= e($r['name']) ?></strong><?= $isSys ? ' <span class="badge">bawaan</span>' : '' ?></td>
      <td data-label="Deskripsi"><?= e($r['description']) ?></td>
      <td data-label="Pengguna"><?= (int)$r['user_count'] ?></td>
      <td data-label="Izin"><?= $r['slug'] === 'super_admin' ? 'Semua' : (int)$r['perm_count'] . ' dari ' . (int)$total ?></td>
      <td class="actions">
        <a class="btn btn--small btn--light" href="/admin/roles/<?= (int)$r['id'] ?>/edit"><?= $r['slug'] === 'super_admin' ? 'Lihat' : 'Atur izin' ?></a>
        <?php if (!$isSys && Auth::can('roles.delete')): ?>
          <form method="post" action="/admin/roles/<?= (int)$r['id'] ?>/delete" data-confirm="Hapus peran ini?">
            <?= csrf_field() ?><button class="btn btn--small btn--danger" type="submit">Hapus</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
