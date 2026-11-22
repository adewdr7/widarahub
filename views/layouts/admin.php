<?php
$user = Auth::user();
$resources = require BASE_PATH . '/app/resources.php';
$path = current_path();
$items = [['Overview', '/admin']];
foreach ($resources as $k => $r) if (Auth::can($r['perm'] . '.view')) $items[] = [$r['label'], '/admin/r/' . $k];
if (Auth::can('media.view')) $items[] = ['Media', '/admin/media'];
if (Auth::can('settings.view')) $items[] = ['Pengaturan Website', '/admin/settings'];
if (Auth::can('roles.view')) $items[] = ['Peran & Izin', '/admin/roles'];
if (Auth::can('audit.view')) $items[] = ['Audit Log', '/admin/audit'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title><?= e($title ?? 'Admin') ?> — <?= e(config('app_name')) ?> Admin</title>
  <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin">
<header class="admin-top">
  <button type="button" id="sideToggle" class="admin-top__menu" aria-label="Menu">☰</button>
  <a class="admin-top__brand" href="/admin"><?= e(config('app_name')) ?> Admin</a>
  <a class="admin-top__view" href="/" target="_blank">Lihat situs ↗</a>
  <span class="admin-top__user"><?= e($user['name'] ?? '') ?> · <?= e($user['role_name'] ?? '') ?></span>
  <form method="post" action="/admin/logout"><?= csrf_field() ?><button class="btn btn--ghost" type="submit">Keluar</button></form>
</header>
<div class="admin-wrap">
  <aside class="admin-side" id="adminSide">
    <nav aria-label="Menu admin">
      <?php foreach ($items as [$label, $href]):
        $active = $href === '/admin' ? $path === '/admin' : strpos($path, $href) === 0; ?>
        <a href="<?= e($href) ?>"<?= $active ? ' class="is-active"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
  </aside>
  <main class="admin-main">
    <?php foreach (flashes() as $f): ?><div class="flash flash--<?= e($f['type']) ?>"><?= e($f['msg']) ?></div><?php endforeach; ?>
    <?= $content ?>
  </main>
</div>
<script src="<?= asset('js/admin.js') ?>" defer></script>
</body>
</html>
