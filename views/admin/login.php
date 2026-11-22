<?php /* Variabel: $error, $username */ ?>
<main class="auth-card">
  <img src="<?= e(media_url('assets/img/brand/logo-widarahub.svg', 'assets/img/brand/logo-icon.png')) ?>" alt="<?= e(config('app_name')) ?>" width="160" height="40">
  <h1>Masuk Dashboard</h1>
  <?php if ($error): ?><div class="flash flash--error" role="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="/admin/login" class="form">
    <?= csrf_field() ?>
    <label>Username <input name="username" value="<?= e($username) ?>" autocomplete="username" required autofocus></label>
    <label>Kata sandi <input type="password" name="password" autocomplete="current-password" required></label>
    <button class="btn" type="submit">Masuk</button>
  </form>
  <p class="muted"><a href="/">← Kembali ke situs</a></p>
</main>
