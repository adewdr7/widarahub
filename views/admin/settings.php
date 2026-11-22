<?php /* Variabel: $groups, $values, $errors */ ?>
<div class="page-head"><h1>Pengaturan Website</h1></div>
<form method="post" action="/admin/settings" enctype="multipart/form-data" class="form form--admin">
  <?= csrf_field() ?>
  <?php foreach ($groups as $groupName => $fields): ?>
    <fieldset class="panel"><legend><?= e($groupName) ?></legend>
      <?php require BASE_PATH . '/views/admin/_fields.php'; ?>
    </fieldset>
  <?php endforeach; ?>
  <?php if (Auth::can('settings.edit')): ?><div class="form-actions"><button class="btn" type="submit">Simpan pengaturan</button></div><?php endif; ?>
</form>
