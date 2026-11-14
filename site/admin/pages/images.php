<?php
defined('ADMIN_PAGE') || exit;

$slots = image_slots();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $slot = (string) ($_POST['slot'] ?? '');
    $anchor = '#slot-' . preg_replace('/[^a-z0-9_-]/i', '', $slot);
    if (!isset($slots[$slot])) {
        flash('error', 'Unknown image.');
        redirect_to('images');
    }
    $over = json_read('images', []);

    if ($action === 'replace') {
        $file = $_FILES['image'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            flash('error', 'Please choose an image first.');
            header('Location: ' . admin_url('images') . $anchor);
            exit;
        }
        try {
            [$src, $thumb] = store_uploaded_image($file, 'site');
        } catch (Throwable $ex) {
            flash('error', $slots[$slot][0] . ': ' . ($ex instanceof RuntimeException ? $ex->getMessage() : 'the image could not be processed.'));
            header('Location: ' . admin_url('images') . $anchor);
            exit;
        }
        $old = $over[$slot] ?? null;
        $over[$slot] = ['src' => $src, 'thumb' => $thumb, 'updated' => now_iso()];
        if (json_write('images', $over)) {
            if ($old) {
                delete_upload($old['src'] ?? null);
                if (($old['thumb'] ?? '') !== ($old['src'] ?? '')) {
                    delete_upload($old['thumb'] ?? null);
                }
            }
            flash('ok', '"' . $slots[$slot][0] . '" has been replaced.');
        } else {
            delete_upload($src);
            delete_upload($thumb);
            flash('error', 'Could not save. Check that the data folder is writable.');
        }
        header('Location: ' . admin_url('images') . $anchor);
        exit;
    }

    if ($action === 'reset') {
        $old = $over[$slot] ?? null;
        unset($over[$slot]);
        if (json_write('images', $over)) {
            if ($old) {
                delete_upload($old['src'] ?? null);
                if (($old['thumb'] ?? '') !== ($old['src'] ?? '')) {
                    delete_upload($old['thumb'] ?? null);
                }
            }
            flash('ok', '"' . $slots[$slot][0] . '" is back to the original photo.');
        } else {
            flash('error', 'Could not save. Check that the data folder is writable.');
        }
        header('Location: ' . admin_url('images') . $anchor);
        exit;
    }

    flash('error', 'Unknown action.');
    redirect_to('images');
}

$groups = [];
foreach ($slots as $key => $def) {
    $groups[$def[1]][$key] = $def;
}
$over = slot_overrides();

admin_head('Site images', 'images');
page_title('Site images', 'Change the main photos used across the website pages. Wide photos work best.');
?>
<?php foreach ($groups as $group => $defs): ?>
<section class="card">
  <h2><?= e($group) ?></h2>
  <div class="slot-grid">
    <?php foreach ($defs as $key => $def): $custom = !empty($over[$key]['src']); ?>
    <div class="slot" id="slot-<?= e($key) ?>">
      <div class="slot-img"><img src="<?= e(img_thumb($key)) ?>" alt="" loading="lazy">
        <?php if ($custom): ?><span class="badge badge-teal g-flag">Your photo</span><?php endif; ?></div>
      <div class="slot-body">
        <strong><?= e($def[0]) ?></strong>
        <span class="muted small"><?= e($def[3]) ?></span>
        <form method="post" action="<?= e(admin_url('images')) ?>" enctype="multipart/form-data" class="slot-form">
          <?= csrf_field() ?><input type="hidden" name="action" value="replace"><input type="hidden" name="slot" value="<?= e($key) ?>">
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required aria-label="New photo for <?= e($def[0]) ?>">
          <button class="btn btn-primary btn-sm" type="submit">Upload</button>
        </form>
        <?php if ($custom): ?>
        <form method="post" action="<?= e(admin_url('images')) ?>" data-confirm="Go back to the original photo for this spot?">
          <?= csrf_field() ?><input type="hidden" name="action" value="reset"><input type="hidden" name="slot" value="<?= e($key) ?>">
          <button class="btn btn-ghost btn-sm" type="submit">Reset to default</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endforeach; ?>
<?php admin_foot();
