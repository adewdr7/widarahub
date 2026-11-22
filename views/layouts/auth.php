<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title><?= e($title ?? 'Masuk') ?> — <?= e(config('app_name')) ?></title>
  <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="auth-body" style="--auth-bg:url('<?= e(media_url('assets/img/auth/login-bg.jpg', 'assets/img/placeholder.svg')) ?>')">
<?= $content ?>
</body>
</html>
