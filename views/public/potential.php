<?php /* Variabel: $item */ ?>
<section class="container section page-layout">
  <article class="prose">
    <?php if ($item['category_name']): ?><span class="tag"><?= e($item['category_name']) ?></span><?php endif; ?>
    <h1><?= e($item['title']) ?></h1>
    <img class="post-hero" src="<?= e(media_url($item['thumbnail'])) ?>" alt="" width="1200" height="800">
    <?= clean_html($item['description']) ?>
    <?php if (is_numeric($item['lat'] ?? null) && is_numeric($item['lng'] ?? null)): ?>
      <h2>Lokasi</h2>
      <?php partial('map', ['markers' => [['lat' => $item['lat'], 'lng' => $item['lng'], 'title' => $item['title']]], 'label' => 'Peta lokasi ' . $item['title']]); ?>
      <p><a class="btn btn--small" href="https://www.google.com/maps/dir/?api=1&amp;destination=<?= (float)$item['lat'] ?>,<?= (float)$item['lng'] ?>" target="_blank" rel="noopener">Buka rute</a></p>
    <?php endif; ?>
  </article>
  <aside class="info-box">
    <h2>Informasi</h2>
    <dl>
      <?php if ($item['location']): ?><dt>Lokasi</dt><dd><?= e($item['location']) ?></dd><?php endif; ?>
      <?php if ($item['hours']): ?><dt>Jam operasional</dt><dd><?= e($item['hours']) ?></dd><?php endif; ?>
      <?php if ($item['contact']): ?><dt>Kontak</dt><dd><?= e($item['contact']) ?></dd><?php endif; ?>
      <?php if ($item['manager']): ?><dt>Pengelola</dt><dd><?= e($item['manager']) ?></dd><?php endif; ?>
      <?php if (!empty($item['products'])): ?><dt>Produk / jasa</dt><dd><?= nl2br(e($item['products'])) ?></dd><?php endif; ?>
      <?php if (!empty($item['price_range'])): ?><dt>Kisaran harga</dt><dd><?= e($item['price_range']) ?></dd><?php endif; ?>
      <?php if (!empty($item['consent_public']) && !empty($item['whatsapp'])): ?><dt>WhatsApp</dt><dd><a href="https://wa.me/<?= e(preg_replace('/\D+/', '', $item['whatsapp'])) ?>" target="_blank" rel="noopener">Hubungi via WhatsApp</a></dd><?php endif; ?>
    </dl>
  </aside>
</section>
