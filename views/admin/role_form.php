<?php /* Variabel: $role, $catalog, $actions, $selected, $errors, $values, $mine, $isSuper, $isSystem, $locked */
$action = $role ? '/admin/roles/' . (int)$role['id'] : '/admin/roles';
$readonly = $locked || ($role && !Auth::can('roles.edit'));
?>
<div class="page-head"><h1><?= e($title) ?></h1><a class="btn btn--light" href="/admin/roles">← Kembali</a></div>
<?php if ($locked): ?><div class="flash">Super Admin selalu memiliki semua izin dan tidak dapat diubah.</div><?php endif; ?>
<?php if (!$isSuper && !$locked): ?><div class="flash">Anda hanya dapat memberi izin yang Anda miliki. Kotak abu-abu tidak dapat Anda ubah.</div><?php endif; ?>

<form method="post" action="<?= e($action) ?>" class="form form--admin">
  <?= csrf_field() ?>
  <div class="field<?= isset($errors['name']) ? ' field--error' : '' ?>">
    <label for="name">Nama peran <span class="req">*</span></label>
    <input id="name" name="name" value="<?= e($values['name'] ?? '') ?>" maxlength="60" <?= ($isSystem || $readonly) ? 'readonly' : '' ?>>
    <?php if (isset($errors['name'])): ?><small class="error"><?= e($errors['name']) ?></small><?php endif; ?>
  </div>
  <div class="field">
    <label for="description">Deskripsi</label>
    <input id="description" name="description" value="<?= e($values['description'] ?? '') ?>" maxlength="200" <?= $readonly ? 'readonly' : '' ?>>
  </div>

  <h2>Izin</h2>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Modul</th><?php foreach ($actions as $al): ?><th><?= e($al) ?></th><?php endforeach; ?></tr></thead>
    <tbody>
    <?php foreach ($catalog as $m => $info): ?>
      <tr>
        <td><strong><?= e($info['label']) ?></strong></td>
        <?php foreach ($actions as $a => $al): ?>
          <td>
          <?php if (isset($info['actions'][$a])):
            $slug = $info['actions'][$a];
            $can = $isSuper || in_array($slug, $mine, true);
            $checked = $locked || in_array($slug, $selected, true); ?>
            <input type="checkbox" name="perm[]" value="<?= e($slug) ?>" aria-label="<?= e($info['label'] . ': ' . $al) ?>"
              <?= $checked ? 'checked' : '' ?> <?= ($locked || $readonly || !$can) ? 'disabled' : '' ?>>
          <?php else: ?>—<?php endif; ?>
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <p class="muted">Tips: izin "Lihat" diperlukan agar menu muncul. "Publikasikan" memungkinkan menyetujui dan menerbitkan konten.</p>
  <?php if (!$readonly): ?><div class="form-actions"><button class="btn" type="submit">Simpan</button><a class="btn btn--light" href="/admin/roles">Batal</a></div><?php endif; ?>
</form>
