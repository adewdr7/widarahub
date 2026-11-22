<?php /* Dipakai oleh form.php dan settings.php. Variabel: $fields, $values, $errors */
foreach ($fields as $f):
    $n = $f['name'];
    $col = $f['column'] ?? $n;
    $v = $values[$col] ?? ($f['default'] ?? '');
    $err = $errors[$n] ?? '';
    $type = $f['type'];
    $id = 'f_' . $n;
?>
<div class="field<?= $err ? ' field--error' : '' ?>">
  <?php if ($type === 'checkbox'): ?>
    <label class="check"><input type="checkbox" id="<?= e($id) ?>" name="<?= e($n) ?>" value="1"<?= $v ? ' checked' : '' ?>> <?= e($f['label']) ?></label>
  <?php else: ?>
    <label for="<?= e($id) ?>"><?= e($f['label']) ?><?= !empty($f['required']) ? ' <span class="req">*</span>' : '' ?></label>
    <?php switch ($type):
      case 'textarea': ?>
        <textarea id="<?= e($id) ?>" name="<?= e($n) ?>" rows="4"><?= e($v) ?></textarea>
      <?php break;
      case 'richtext': ?>
        <div class="rte" data-rte>
          <div class="rte__bar" hidden>
            <button type="button" data-cmd="bold" title="Tebal"><b>B</b></button>
            <button type="button" data-cmd="italic" title="Miring"><i>I</i></button>
            <button type="button" data-cmd="formatBlock:h2" title="Subjudul">H2</button>
            <button type="button" data-cmd="insertUnorderedList" title="Daftar">• List</button>
            <button type="button" data-cmd="insertOrderedList" title="Daftar angka">1. List</button>
            <button type="button" data-cmd="createLink" title="Tautan">Link</button>
            <button type="button" data-cmd="removeFormat" title="Bersihkan">Tx</button>
          </div>
          <div class="rte__area" contenteditable="true" hidden><?= clean_html((string)$v) ?></div>
          <textarea id="<?= e($id) ?>" name="<?= e($n) ?>" rows="10"><?= e($v) ?></textarea>
        </div>
      <?php break;
      case 'select': ?>
        <select id="<?= e($id) ?>" name="<?= e($n) ?>">
          <?php foreach ($f['options'] as $ok => $ol): ?><option value="<?= e($ok) ?>"<?= (string)$v === (string)$ok ? ' selected' : '' ?>><?= e($ol) ?></option><?php endforeach; ?>
        </select>
      <?php break;
      case 'relation': $opts = ResourceController::relationOptions($f); ?>
        <select id="<?= e($id) ?>" name="<?= e($n) ?>">
          <option value="">— pilih —</option>
          <?php foreach ($opts as $ok => $ol): ?><option value="<?= e($ok) ?>"<?= (string)$v === (string)$ok ? ' selected' : '' ?>><?= e($ol) ?></option><?php endforeach; ?>
        </select>
      <?php break;
      case 'image': case 'file': ?>
        <?php if ($v): ?>
          <div class="current-file">
            <?php if ($type === 'image'): ?><img src="<?= e(media_url((string)$v)) ?>" alt="" height="80"><?php endif; ?>
            <small class="muted">Saat ini: <?= e($v) ?></small>
          </div>
        <?php endif; ?>
        <input type="file" id="<?= e($id) ?>" name="<?= e($n) ?>" accept="<?= $type === 'image' ? 'image/jpeg,image/png,image/webp,image/gif' : '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,image/*' ?>">
      <?php break;
      case 'password': ?>
        <input type="password" id="<?= e($id) ?>" name="<?= e($n) ?>" autocomplete="new-password">
      <?php break;
      case 'readonly': ?>
        <div class="readonly"><?= nl2br(e($v)) ?></div>
      <?php break;
      case 'number': ?>
        <input type="number" id="<?= e($id) ?>" name="<?= e($n) ?>" value="<?= e($v) ?>">
      <?php break;
      case 'date': ?>
        <input type="date" id="<?= e($id) ?>" name="<?= e($n) ?>" value="<?= e($v ? substr((string)$v, 0, 10) : '') ?>">
      <?php break;
      case 'datetime':
        $dv = (string)$v;
        if ($dv !== '' && strpos($dv, ' ') !== false) $dv = str_replace(' ', 'T', substr($dv, 0, 16)); ?>
        <input type="datetime-local" id="<?= e($id) ?>" name="<?= e($n) ?>" value="<?= e($dv) ?>">
      <?php break;
      default: ?>
        <input type="text" id="<?= e($id) ?>" name="<?= e($n) ?>" value="<?= e($v) ?>">
    <?php endswitch; ?>
  <?php endif; ?>
  <?php if (!empty($f['hint'])): ?><small class="muted"><?= e($f['hint']) ?></small><?php endif; ?>
  <?php if ($err): ?><small class="error"><?= e($err) ?></small><?php endif; ?>
</div>
<?php endforeach; ?>
