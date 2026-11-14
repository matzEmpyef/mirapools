<?php
defined('ADMIN_PAGE') || exit;

$cats = gallery_categories();
$filter = (string) ($_GET['cat'] ?? $_POST['cat'] ?? '');
if (!isset($cats[$filter])) {
    $filter = '';
}
$back = fn() => redirect_to('gallery', $filter !== '' ? ['cat' => $filter] : []);

function gallery_index(array $items, string $id): int
{
    foreach ($items as $i => $it) {
        if (($it['id'] ?? '') === $id) {
            return $i;
        }
    }
    return -1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $items = gallery_all();

    if ($action === 'upload') {
        $files = normalise_files('photos');
        $cat = (string) ($_POST['category'] ?? '');
        if (!isset($cats[$cat])) {
            flash('error', 'Please choose a category for the photos.');
            $back();
        }
        if (!$files) {
            flash('error', 'Please choose at least one photo.');
            $back();
        }
        $title = clean_text($_POST['title'] ?? '', 120);
        $location = clean_text($_POST['location'] ?? '', 120);
        $featured = !empty($_POST['featured']);
        $altBase = $title !== '' ? $title : $cats[$cat];
        $added = [];
        $errors = [];
        foreach ($files as $f) {
            try {
                [$src, $thumb, $w, $h] = store_uploaded_image($f, 'gallery');
            } catch (Throwable $ex) {
                $errors[] = safe_label($f['name']) . ': ' . ($ex instanceof RuntimeException ? $ex->getMessage() : 'could not be processed.');
                continue;
            }
            $added[] = [
                'id'       => 'g' . bin2hex(random_bytes(5)),
                'src'      => $src,
                'thumb'    => $thumb,
                'title'    => $title,
                'alt'      => $altBase . ' by Mira Pools, Kerala',
                'category' => $cat,
                'location' => $location,
                'featured' => $featured,
                'width'    => $w,
                'height'   => $h,
                'created'  => now_iso(),
            ];
        }
        if ($added) {
            // Keep the order the owner chose in the file picker, newest batch on top.
            if (!gallery_save(array_merge($added, $items))) {
                foreach ($added as $a) {
                    delete_upload($a['src']);
                    delete_upload($a['thumb']);
                }
                flash('error', 'The photos were uploaded but could not be saved. Check that the data folder is writable.');
                $back();
            }
            flash('ok', count($added) . (count($added) === 1 ? ' photo' : ' photos') . ' added to the gallery.');
        }
        foreach ($errors as $err) {
            flash('error', $err);
        }
        $back();
    }

    if ($action === 'update') {
        $i = gallery_index($items, (string) ($_POST['id'] ?? ''));
        if ($i < 0) {
            flash('error', 'That photo no longer exists.');
            $back();
        }
        $cat = (string) ($_POST['category'] ?? '');
        $title = clean_text($_POST['title'] ?? '', 120);
        $alt = clean_text($_POST['alt'] ?? '', 200);
        $items[$i]['title'] = $title;
        $items[$i]['alt'] = $alt !== '' ? $alt : (($title !== '' ? $title : ($cats[$cat] ?? 'Photo')) . ' by Mira Pools, Kerala');
        $items[$i]['category'] = isset($cats[$cat]) ? $cat : ($items[$i]['category'] ?? array_key_first($cats));
        $items[$i]['location'] = clean_text($_POST['location'] ?? '', 120);
        $items[$i]['featured'] = !empty($_POST['featured']);
        gallery_save($items) ? flash('ok', 'Photo details saved.') : flash('error', 'Could not save. Check that the data folder is writable.');
        $back();
    }

    if ($action === 'delete') {
        $i = gallery_index($items, (string) ($_POST['id'] ?? ''));
        if ($i < 0) {
            flash('error', 'That photo no longer exists.');
            $back();
        }
        $gone = $items[$i];
        array_splice($items, $i, 1);
        if (gallery_save($items)) {
            delete_upload($gone['src'] ?? null);
            if (($gone['thumb'] ?? '') !== ($gone['src'] ?? '')) {
                delete_upload($gone['thumb'] ?? null);
            }
            flash('ok', 'Photo deleted.');
        } else {
            flash('error', 'Could not delete. Check that the data folder is writable.');
        }
        $back();
    }

    if ($action === 'move') {
        $id = (string) ($_POST['id'] ?? '');
        $dir = ($_POST['dir'] ?? '') === 'up' ? -1 : 1;
        $visible = [];
        foreach ($items as $i => $it) {
            if ($filter === '' || ($it['category'] ?? '') === $filter) {
                $visible[] = $i;
            }
        }
        $pos = array_search(gallery_index($items, $id), $visible, true);
        if ($pos !== false && isset($visible[$pos + $dir])) {
            [$a, $b] = [$visible[$pos], $visible[$pos + $dir]];
            [$items[$a], $items[$b]] = [$items[$b], $items[$a]];
            gallery_save($items);
        }
        $back();
    }

    if ($action === 'reorder') {
        // Posted ids take the slots the same ids already hold, so a filtered view is safe.
        $order = $_POST['order'] ?? [];
        $order = is_array($order) ? array_values(array_unique(array_map('strval', $order))) : [];
        $slots = [];
        $byId = [];
        foreach ($items as $i => $it) {
            if (in_array($it['id'] ?? '', $order, true)) {
                $slots[] = $i;
                $byId[$it['id']] = $it;
            }
        }
        $order = array_values(array_filter($order, fn($id) => isset($byId[$id])));
        if (count($order) === count($slots)) {
            foreach ($slots as $n => $slot) {
                $items[$slot] = $byId[$order[$n]];
            }
            $ok = gallery_save($items);
        } else {
            $ok = false;
        }
        if (is_ajax()) {
            json_reply(['ok' => $ok], $ok ? 200 : 400);
        }
        $ok ? flash('ok', 'Order saved.') : flash('error', 'Could not save the new order.');
        $back();
    }

    flash('error', 'Unknown action.');
    $back();
}

$all = gallery_all();
$counts = array_count_values(array_map(fn($i) => (string) ($i['category'] ?? ''), $all));
$items = $filter === '' ? $all : array_values(array_filter($all, fn($i) => ($i['category'] ?? '') === $filter));
$self = admin_url('gallery', $filter !== '' ? ['cat' => $filter] : []);

admin_head('Gallery', 'gallery');
page_title('Gallery', 'The photos shown on the Gallery page. Photos marked "home page" also appear on the home page.');
?>
<section class="card">
  <div class="card-head">
    <h2>All photos <span class="muted">(<?= count($items) ?>)</span></h2>
    <button type="button" class="btn btn-primary" data-modal-open="upload-modal">
      <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg> Add photos
    </button>
  </div>
  <div class="card-head card-head-sub">
    <nav class="chips" aria-label="Filter by category">
      <a class="chip<?= $filter === '' ? ' on' : '' ?>" href="<?= e(admin_url('gallery')) ?>">All <?= count($all) ?></a>
      <?php foreach ($cats as $k => $label): ?>
        <a class="chip<?= $filter === $k ? ' on' : '' ?>" href="<?= e(admin_url('gallery', ['cat' => $k])) ?>"><?= e($label) ?> <?= (int) ($counts[$k] ?? 0) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
  <?php if (!$items): ?>
    <p class="muted">No photos here yet. Use "Add photos" to add some.</p>
  <?php else: ?>
  <p class="muted small hide-touch">Tip: drag a photo to a new place to change the order. On a phone, use the arrow buttons.</p>
  <div class="g-grid" data-sortable data-post="<?= e($self) ?>" data-csrf="<?= e(csrf_token()) ?>">
    <?php foreach ($items as $n => $it):
        $id = (string) ($it['id'] ?? '');
        $title = (string) ($it['title'] ?? '');
        $thumb = (string) ($it['thumb'] ?? $it['src'] ?? '');
        $catLabel = $cats[$it['category'] ?? ''] ?? (string) ($it['category'] ?? ''); ?>
    <article class="g-card" draggable="true" data-id="<?= e($id) ?>"
             data-title="<?= e($title) ?>" data-alt="<?= e((string) ($it['alt'] ?? '')) ?>"
             data-category="<?= e((string) ($it['category'] ?? '')) ?>" data-location="<?= e((string) ($it['location'] ?? '')) ?>"
             data-featured="<?= !empty($it['featured']) ? '1' : '0' ?>" data-thumb="<?= e($thumb) ?>">
      <button type="button" class="g-thumb" data-edit aria-label="Edit <?= e($title !== '' ? $title : 'photo') ?>">
        <img src="<?= e($thumb) ?>" alt="" loading="lazy" draggable="false">
        <?php if (!empty($it['featured'])): ?><span class="badge badge-gold g-flag">Home page</span><?php endif; ?>
        <span class="g-hover">Edit</span>
        <span class="g-pen" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 20h4L19 9l-4-4L4 16v4zM14 6l4 4"/></svg></span>
      </button>
      <div class="g-body">
        <strong class="g-title" title="<?= e($title) ?>"><?= e($title !== '' ? $title : 'Untitled') ?></strong>
        <span class="g-meta"><?= e($catLabel) ?><?= !empty($it['location']) ? ' · ' . e((string) $it['location']) : '' ?></span>
      </div>
      <div class="g-actions">
        <form method="post" action="<?= e($self) ?>" class="inline">
          <?= csrf_field() ?><input type="hidden" name="action" value="move"><input type="hidden" name="id" value="<?= e($id) ?>"><input type="hidden" name="cat" value="<?= e($filter) ?>">
          <button class="btn btn-sm btn-icon" name="dir" value="up" aria-label="Move earlier"<?= $n === 0 ? ' disabled' : '' ?>>&larr;</button>
          <button class="btn btn-sm btn-icon" name="dir" value="down" aria-label="Move later"<?= $n === count($items) - 1 ? ' disabled' : '' ?>>&rarr;</button>
        </form>
        <span class="g-actions-right">
          <button type="button" class="btn btn-sm" data-edit>Edit</button>
          <form method="post" action="<?= e($self) ?>" class="inline" data-confirm="Delete this photo from the website?">
            <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($id) ?>"><input type="hidden" name="cat" value="<?= e($filter) ?>">
            <button class="btn btn-sm btn-danger btn-icon" type="submit" aria-label="Delete photo">
              <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/></svg>
            </button>
          </form>
        </span>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php modal_open('upload-modal', 'Add photos', 'Pick one or more photos. JPG, PNG or WebP, up to ' . (int) (MAX_UPLOAD_BYTES / 1048576) . ' MB each.', 'lg'); ?>
  <form method="post" action="<?= e($self) ?>" enctype="multipart/form-data" data-upload-form>
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="upload">
    <label class="dropzone" data-dropzone>
      <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" required data-file-input>
      <svg class="dz-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4M7 9l5-5 5 5M4 16v4h16v-4"/></svg>
      <span class="dz-title">Choose photos or drop them here</span>
      <span class="dz-sub" data-dz-count>You can pick several at once.</span>
    </label>
    <div class="previews" data-previews></div>
    <div class="form-row">
      <label class="field"><span>Category</span>
        <select name="category" required>
          <?php foreach ($cats as $k => $label): ?>
            <option value="<?= e($k) ?>"<?= $filter === $k ? ' selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select></label>
      <label class="field"><span>Title <i>(optional)</i></span>
        <input type="text" name="title" maxlength="120" placeholder="e.g. Villa pool with granite coping"></label>
      <label class="field"><span>Place <i>(optional)</i></span>
        <input type="text" name="location" maxlength="120" placeholder="e.g. Kakkanad, Kochi"></label>
    </div>
    <p class="muted small" style="margin-top:-6px">With several photos, they all get this title. You can edit each one afterwards.</p>
    <label class="check"><input type="checkbox" name="featured" value="1"> <span>Show on home page</span></label>
    <div class="modal-foot">
      <button type="button" class="btn" data-modal-close>Cancel</button>
      <button class="btn btn-primary" type="submit">Upload photos</button>
    </div>
  </form>
<?php modal_close(); ?>

<?php modal_open('edit-modal', 'Edit photo', '', 'md'); ?>
  <form method="post" action="<?= e($self) ?>" class="edit-form" data-edit-form>
    <?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value=""><input type="hidden" name="cat" value="<?= e($filter) ?>">
    <div class="edit-grid">
      <figure class="edit-preview"><img src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" alt="" data-edit-img></figure>
      <div>
        <label class="field"><span>Title</span><input type="text" name="title" maxlength="120"></label>
        <label class="field"><span>Category</span>
          <select name="category">
            <?php foreach ($cats as $k => $label): ?>
              <option value="<?= e($k) ?>"><?= e($label) ?></option>
            <?php endforeach; ?>
          </select></label>
        <label class="field"><span>Place</span><input type="text" name="location" maxlength="120" placeholder="e.g. Kakkanad, Kochi"></label>
      </div>
    </div>
    <label class="field"><span>Description for screen readers and Google</span><input type="text" name="alt" maxlength="200"><small>Describe what is in the photo. Leave empty to use the title.</small></label>
    <label class="check"><input type="checkbox" name="featured" value="1"> <span>Show on home page</span></label>
    <div class="modal-foot">
      <button type="button" class="btn" data-modal-close>Cancel</button>
      <button class="btn btn-primary" type="submit">Save changes</button>
    </div>
  </form>
<?php modal_close(); ?>
<?php admin_foot();
