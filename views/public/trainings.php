<?php /* Variabel: $items, $counts */ ?>
<link rel="stylesheet" href="<?= asset('css/keuangan.css') ?>">
<section class="container section fin">
  <h1>Pelatihan & Pengembangan SDM</h1>
  <p class="muted">Pelatihan untuk meningkatkan keterampilan warga agar usaha dan layanan di desa makin beragam. Data pendaftaran hanya dilihat petugas desa yang berwenang.</p>
  <?php foreach ($items as $t):
    $taken = (int)($counts[(int)$t['id']] ?? 0);
    $quota = (int)$t['quota'];
    $full = $quota > 0 && $taken >= $quota;
    $open = $t['is_open'] && !$full; ?>
    <article class="list-item">
      <?php if ($t['goal_title']): ?><span class="tag">Sasaran: <?= e($t['goal_title']) ?></span><?php endif; ?>
      <h2><?= e($t['title']) ?></h2>
      <p class="muted">
        <?= $t['starts_at'] ? e(id_date($t['starts_at'], true)) : 'Jadwal menyusul' ?>
        <?= $t['location'] ? ' · ' . e($t['location']) : '' ?>
        · <?= $quota > 0 ? $taken . ' dari ' . $quota . ' peserta' : $taken . ' peminat' ?>
      </p>
      <div class="prose"><?= clean_html($t['description']) ?></div>
      <?php if ($open): ?>
        <details class="interest">
          <summary>Daftar pelatihan ini</summary>
          <form method="post" action="/minat" class="form">
            <?= csrf_field() ?>
            <input type="hidden" name="ref_type" value="training"><input type="hidden" name="ref_id" value="<?= (int)$t['id'] ?>">
            <div class="hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
            <label>Nama <input name="name" maxlength="100" required autocomplete="name"></label>
            <label>No. HP / WhatsApp <input name="contact" maxlength="100" required></label>
            <label>Catatan (opsional) <textarea name="note" rows="3" maxlength="500"></textarea></label>
            <label class="check"><input type="checkbox" name="consent" value="1" required> Saya setuju data saya dipakai petugas desa hanya untuk menghubungi saya terkait pelatihan ini.</label>
            <button class="btn" type="submit">Daftar</button>
          </form>
        </details>
      <?php else: ?><p><strong><?= $full ? 'Kuota penuh' : 'Pendaftaran ditutup' ?></strong></p><?php endif; ?>
    </article>
  <?php endforeach; ?>
  <?php if (!$items): ?><p class="muted">Belum ada pelatihan yang dijadwalkan.</p><?php endif; ?>
  <p><a class="link-more" href="/keuangan">← Keuangan & Tujuan Desa</a></p>
</section>
