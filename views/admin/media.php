<?php /* Variabel: $files, $pg, $q, $folder, $folders */ ?>
<div class="page-head"><h1>Media</h1></div>

<?php if (Auth::can('media.create')): ?>
<form class="toolbar" method="post" action="/admin/media/upload" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <input type="file" name="file" required accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
  <input name="folder" placeholder="Folder (mis. berita)" list="folders">
  <datalist id="folders"><?php foreach ($folders as $fo): ?><option value="<?= e($fo) ?>"><?php endforeach; ?></datalist>
  <button class="btn" type="submit">Unggah</button>
</form>
<?php endif; ?>

<form class="toolbar" method="get" action="/admin/media">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari nama file…">
  <select name="folder"><option value="">Semua folder</option>
    <?php foreach ($folders as $fo): ?><option value="<?= e($fo) ?>"<?= $folder === $fo ? ' selected' : '' ?>><?= e($fo) ?></option><?php endforeach; ?>
  </select>
  <button class="btn btn--light" type="submit">Filter</button>
</form>

<div class="media-grid">
<?php foreach ($files as $m):
  $isImg = (bool)preg_match('/\.(jpe?g|png|webp|gif)$/i', $m['path']); ?>
  <figure class="media-item">
    <?php if ($isImg): ?><img src="<?= e(media_url($m['path'])) ?>" alt="" loading="lazy"><?php else: ?><div class="media-item__doc">📄</div><?php endif; ?>
    <figcaption>
      <strong><?= e($m['original_name']) ?></strong>
      <small class="muted"><?= e($m['folder']) ?> · <?= round(((int)$m['size']) / 1024) ?> KB</small>
      <input class="copy" readonly value="<?= e($m['path']) ?>" onclick="this.select()">
      <div class="actions">
        <?php if ($isImg && Auth::can('media.create')): ?>
          <form method="post" action="/admin/media/<?= (int)$m['id'] ?>/replace" enctype="multipart/form-data">
            <?= csrf_field() ?><input type="file" name="file" accept="image/*" required><button class="btn btn--small btn--light">Ganti</button>
          </form>
        <?php endif; ?>
        <?php if (Auth::can('media.delete')): ?>
          <form method="post" action="/admin/media/<?= (int)$m['id'] ?>/delete" data-confirm="Hapus file ini permanen?">
            <?= csrf_field() ?><button class="btn btn--small btn--danger">Hapus</button>
          </form>
        <?php endif; ?>
      </div>
    </figcaption>
  </figure>
<?php endforeach; ?>
<?php if (!$files): ?><p class="muted">Belum ada file.</p><?php endif; ?>
</div>
<?php partial('pagination', ['pg' => $pg]); ?>
