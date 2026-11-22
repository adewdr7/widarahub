<?php /* Variabel: $stats, $pending, $completeness, $events, $recent, $defaultPw */ ?>
<h1>Overview</h1>
<?php if ($defaultPw): ?>
  <div class="flash flash--error">Kata sandi akun <strong>admin</strong> masih bawaan. Segera ganti di menu Pengguna.</div>
<?php endif; ?>

<div class="cards">
  <?php foreach ($stats as $label => $n): ?>
    <div class="stat-card"><strong><?= (int)$n ?></strong><span><?= e($label) ?></span></div>
  <?php endforeach; ?>
</div>

<div class="panels">
  <section class="panel">
    <h2>Kelengkapan profil desa</h2>
    <ul class="checklist">
      <?php foreach ($completeness as $label => $ok): ?>
        <li class="<?= $ok ? 'is-ok' : 'is-todo' ?>"><?= $ok ? '✔' : '○' ?> <?= e($label) ?>: <?= $ok ? 'Lengkap' : 'Belum lengkap' ?></li>
      <?php endforeach; ?>
    </ul>
    <p class="muted">Konten bertanda [DEMO] dihitung belum lengkap.</p>
  </section>

  <section class="panel">
    <h2>Menunggu persetujuan</h2>
    <?php foreach ($pending as $p): ?>
      <p><a href="<?= e($p['url']) ?>"><?= e($p['title']) ?></a> <span class="badge"><?= e($p['label']) ?></span> <span class="badge badge--<?= e($p['status']) ?>"><?= e(status_label($p['status'])) ?></span></p>
    <?php endforeach; ?>
    <?php if (!$pending): ?><p class="muted">Tidak ada konten yang menunggu.</p><?php endif; ?>
  </section>

  <section class="panel">
    <h2>Agenda terdekat</h2>
    <?php foreach ($events as $ev): ?><p><?= e($ev['title']) ?><br><span class="muted"><?= e(id_date($ev['starts_at'], true)) ?></span></p><?php endforeach; ?>
    <?php if (!$events): ?><p class="muted">Belum ada agenda.</p><?php endif; ?>
  </section>

  <?php if ($recent): ?>
  <section class="panel">
    <h2>Aktivitas terakhir</h2>
    <?php foreach ($recent as $r): ?>
      <p class="small"><?= e($r['username']) ?> · <?= e($r['action']) ?> <?= e($r['table_name']) ?><br><span class="muted"><?= e(id_date($r['created_at'], true)) ?></span></p>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>
</div>
