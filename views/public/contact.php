<?php /* Variabel: $ticket (array|false|null), $lookup */ $v = village(); ?>
<section class="container section two-col">
  <div>
    <h1>Kontak</h1>
    <dl class="contact-list">
      <dt>Alamat kantor</dt><dd><?= $v['address'] ? nl2br(e($v['address'])) : '<span class="muted">Belum diisi</span>' ?></dd>
      <dt>Telepon</dt><dd><?= $v['phone'] ? e($v['phone']) : '<span class="muted">Belum diisi</span>' ?></dd>
      <dt>Email</dt><dd><?= $v['email'] ? e($v['email']) : '<span class="muted">Belum diisi</span>' ?></dd>
      <?php if ($v['whatsapp']): ?><dt>WhatsApp</dt><dd><a href="https://wa.me/<?= e(preg_replace('/\D+/', '', $v['whatsapp'])) ?>" rel="noopener"><?= e($v['whatsapp']) ?></a></dd><?php endif; ?>
      <dt>Jam pelayanan</dt><dd><?= $v['office_hours'] ? nl2br(e($v['office_hours'])) : '<span class="muted">Belum diisi</span>' ?></dd>
    </dl>

    <?php if (is_numeric($v['maps_lat'] ?? null) && is_numeric($v['maps_lng'] ?? null)): $lat = (float)$v['maps_lat']; $lng = (float)$v['maps_lng']; ?>
      <h2>Lokasi kantor desa</h2>
      <?php partial('map', ['markers' => [['lat' => $lat, 'lng' => $lng, 'title' => 'Kantor ' . $v['name']]], 'label' => 'Peta lokasi kantor desa']); ?>
      <p>
        <a class="btn btn--small" href="https://www.google.com/maps/dir/?api=1&amp;destination=<?= $lat ?>,<?= $lng ?>" target="_blank" rel="noopener">Buka rute</a>
        <a class="btn btn--small btn--light" href="https://www.openstreetmap.org/?mlat=<?= $lat ?>&amp;mlon=<?= $lng ?>#map=17/<?= $lat ?>/<?= $lng ?>" target="_blank" rel="noopener">Lihat di OpenStreetMap</a>
      </p>
    <?php endif; ?>

    <h2>Cek status tiket</h2>
    <form method="get" action="/kontak" class="search-form">
      <label class="sr-only" for="tiket">Nomor tiket</label>
      <input id="tiket" name="tiket" value="<?= e($lookup) ?>" placeholder="WH-20260101-ABC123">
      <button class="btn" type="submit">Cek</button>
    </form>
    <?php if ($ticket === false): ?><p class="flash flash--error">Nomor tiket tidak ditemukan.</p>
    <?php elseif (is_array($ticket)): ?>
      <div class="info-box">
        <p><strong><?= e($ticket['ticket']) ?></strong> — <?= e($ticket['subject']) ?></p>
        <p>Status: <strong><?= e(config('complaint_status')[$ticket['status']] ?? $ticket['status']) ?></strong> · Masuk <?= e(id_date($ticket['created_at'])) ?></p>
        <?php if ($ticket['response']): ?><p><?= nl2br(e($ticket['response'])) ?></p><?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <div id="form">
    <h2>Pengaduan & Aspirasi</h2>
    <p class="muted">Jangan mencantumkan NIK, nomor KK, atau data pribadi sensitif lainnya.</p>
    <form method="post" action="/kontak" class="form">
      <?= csrf_field() ?>
      <div class="hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
      <label>Jenis
        <select name="type"><option value="pengaduan">Pengaduan</option><option value="aspirasi">Aspirasi</option><option value="usulan">Usulan layanan / usaha desa</option></select>
      </label>
      <label>Nama (opsional) <input name="name" maxlength="100" autocomplete="name"></label>
      <label>Kontak balasan (opsional) <input name="contact" maxlength="100" placeholder="No. HP / email"></label>
      <label>Perihal <input name="subject" maxlength="150" required></label>
      <label>Isi laporan <textarea name="message" rows="6" minlength="10" maxlength="3000" required></textarea></label>
      <button class="btn" type="submit">Kirim</button>
    </form>
  </div>
</section>
