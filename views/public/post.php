<?php /* Variabel: $post, $related */
$shareUrl = urlencode(base_url() . '/informasi/' . $post['slug']);
$shareText = urlencode($post['title']);
?>
<section class="container section page-layout">
  <article class="prose">
    <?php if ($post['category_name']): ?><a class="tag" href="/informasi?kategori=<?= e($post['category_slug']) ?>"><?= e($post['category_name']) ?></a><?php endif; ?>
    <h1><?= e($post['title']) ?></h1>
    <time class="muted" datetime="<?= e(substr((string)$post['published_at'], 0, 10)) ?>"><?= e(id_date($post['published_at'], true)) ?></time>
    <img class="post-hero" src="<?= e(media_url($post['thumbnail'])) ?>" alt="" width="1200" height="675">
    <?= clean_html($post['body']) ?>
    <p class="share">Bagikan:
      <a href="https://wa.me/?text=<?= $shareText ?>%20<?= $shareUrl ?>" target="_blank" rel="noopener">WhatsApp</a> ·
      <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener">Facebook</a>
    </p>
  </article>
  <aside>
    <h2>Berita terkait</h2>
    <?php foreach ($related as $p): ?><a class="side-link" href="/informasi/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a><?php endforeach; ?>
    <?php if (!$related): ?><p class="muted">Belum ada berita terkait.</p><?php endif; ?>
  </aside>
</section>
