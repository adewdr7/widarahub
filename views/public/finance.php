<?php
/* Variabel: $years, $year, $sum, $groups, $goals, $needs */
$rp = function ($n) { return 'Rp ' . number_format((int)$n, 0, ',', '.'); };
$pct = function ($a, $b) { return $b > 0 ? (int)min(100, round($a / $b * 100)) : 0; };
$kinds = config('finance_kinds');
$pillars = config('goal_pillars');
$needStatus = config('need_status');
$needCats = config('need_categories');
$byPillar = [];
foreach ($goals as $g) $byPillar[$g['pillar']][] = $g;
$byStatus = [];
foreach ($needs as $n) $byStatus[$n['status_need']][] = $n;
$diff = $sum['pendapatan']['planned'] - $sum['belanja']['planned'];
?>
<link rel="stylesheet" href="<?= asset('css/keuangan.css') ?>">

<section class="container section fin">
  <h1>Keuangan & Tujuan Desa</h1>
  <p class="muted">Ringkasan anggaran, sasaran pembangunan, dan layanan yang masih dibutuhkan agar perputaran ekonomi tetap di desa. Hanya data yang sudah diverifikasi yang ditampilkan.</p>

  <?php if ($years): ?>
  <nav class="filter-bar" aria-label="Tahun anggaran">
    <?php foreach ($years as $y): ?><a href="/keuangan?tahun=<?= (int)$y ?>"<?= $y === $year ? ' aria-current="true"' : '' ?>><?= (int)$y ?></a><?php endforeach; ?>
  </nav>
  <?php endif; ?>

  <h2>Ringkasan anggaran <?= (int)$year ?></h2>
  <?php if (!$groups): ?><p class="muted">Data keuangan tahun ini belum dipublikasikan.</p><?php else: ?>
  <div class="grid grid--4">
    <?php foreach ($kinds as $k => $label): ?>
      <div class="stat">
        <span><?= e($label) ?></span>
        <strong><?= e($rp($sum[$k]['planned'])) ?></strong>
        <small class="muted">Realisasi <?= e($rp($sum[$k]['realized'])) ?> (<?= $pct($sum[$k]['realized'], $sum[$k]['planned']) ?>%)</small>
        <progress max="100" value="<?= $pct($sum[$k]['realized'], $sum[$k]['planned']) ?>" aria-label="Realisasi <?= e($label) ?>"></progress>
      </div>
    <?php endforeach; ?>
    <div class="stat"><span>Selisih pendapatan − belanja</span><strong><?= e($rp($diff)) ?></strong></div>
  </div>

  <?php foreach ($kinds as $k => $label): if (empty($groups[$k])) continue; ?>
    <h3 class="fin__kind"><?= e($label) ?></h3>
    <?php foreach ($groups[$k] as $sector => $items): ?>
      <div class="fin-table-wrap">
        <table class="fin-table">
          <caption><?= e($sector) ?></caption>
          <thead><tr><th>Uraian</th><th>Anggaran</th><th>Realisasi</th><th>%</th></tr></thead>
          <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= e($it['title']) ?><?php if ($it['note']): ?><br><small class="muted"><?= e($it['note']) ?></small><?php endif; ?></td>
              <td><?= e($rp($it['planned'])) ?></td><td><?= e($rp($it['realized'])) ?></td><td><?= $pct($it['realized'], $it['planned']) ?>%</td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>
  <?php endforeach; ?>
  <p><a class="link-more" href="/transparansi">Dokumen APBDes lengkap →</a></p>
  <?php endif; ?>
</section>

<section class="section section--tint fin"><div class="container">
  <h2>Tujuan & Sasaran Desa</h2>
  <?php if (!$goals): ?><p class="muted">Sasaran desa belum dipublikasikan.</p><?php endif; ?>
  <div class="grid grid--3">
  <?php foreach ($pillars as $pk => $plabel): if (empty($byPillar[$pk])) continue; ?>
    <article class="card"><div class="card__body">
      <span class="tag"><?= e($plabel) ?></span>
      <?php foreach ($byPillar[$pk] as $g):
        $den = (int)$g['target_value'] - (int)$g['baseline_value'];
        $p = $den != 0 ? (int)max(0, min(100, round(((int)$g['current_value'] - (int)$g['baseline_value']) / $den * 100))) : 0; ?>
        <h3><?= e($g['title']) ?></h3>
        <?php if ($g['description']): ?><p><?= e($g['description']) ?></p><?php endif; ?>
        <p class="muted"><?= e($g['indicator']) ?>: <strong><?= (int)$g['current_value'] ?></strong> dari target <?= (int)$g['target_value'] ?> <?= e($g['unit']) ?><?= $g['target_year'] ? ' (' . (int)$g['target_year'] . ')' : '' ?></p>
        <progress max="100" value="<?= $p ?>" aria-label="Progres sasaran"></progress> <small><?= $p ?>%</small>
      <?php endforeach; ?>
    </div></article>
  <?php endforeach; ?>
  </div>
</div></section>

<section class="container section fin" id="kebutuhan">
  <h2>Kebutuhan & Peluang Layanan Desa</h2>
  <p class="muted">Layanan, usaha, dan fasilitas yang masih dibutuhkan warga. Semakin banyak yang tersedia di desa, semakin banyak uang warga yang berputar di desa sendiri. Berminat merintis atau punya usulan? <a href="/kontak#form">Kirim usulan</a>. Lihat juga <a href="/pelatihan">pelatihan pengembangan SDM</a>.</p>
  <?php foreach ($needStatus as $sk => $slabel): if (empty($byStatus[$sk])) continue; ?>
    <h3><?= e($slabel) ?></h3>
    <?php foreach ($byStatus[$sk] as $n): ?>
      <article class="list-item<?= (int)$n['priority'] > 0 && $sk === 'dibutuhkan' ? ' list-item--important' : '' ?>">
        <span class="tag"><?= e($needCats[$n['category']] ?? $n['category']) ?></span>
        <h3><?= e($n['title']) ?></h3>
        <?php if ($n['description']): ?><p><?= e($n['description']) ?></p><?php endif; ?>
        <?php if ($n['contact'] && $sk !== 'tersedia'): ?><p class="muted">Kontak bagi yang berminat: <?= e($n['contact']) ?></p><?php endif; ?>
        <?php if ($sk !== 'tersedia'): $c = (int)($interestCounts[(int)$n['id']] ?? 0); ?>
          <?php if ($c > 0): ?><p><strong><?= $c ?></strong> warga sudah menyatakan minat.</p><?php endif; ?>
          <details class="interest">
            <summary>Saya berminat / ingin merintis</summary>
            <form method="post" action="/minat" class="form">
              <?= csrf_field() ?>
              <input type="hidden" name="ref_type" value="need"><input type="hidden" name="ref_id" value="<?= (int)$n['id'] ?>">
              <div class="hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
              <label>Nama <input name="name" maxlength="100" required autocomplete="name"></label>
              <label>No. HP / WhatsApp <input name="contact" maxlength="100" required></label>
              <label>Catatan (opsional) <textarea name="note" rows="3" maxlength="500"></textarea></label>
              <label class="check"><input type="checkbox" name="consent" value="1" required> Saya setuju data saya dipakai petugas desa hanya untuk menghubungi saya terkait hal ini.</label>
              <button class="btn" type="submit">Kirim</button>
            </form>
          </details>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  <?php endforeach; ?>
  <?php if (!$needs): ?><p class="muted">Belum ada data kebutuhan dan peluang.</p><?php endif; ?>
</section>
