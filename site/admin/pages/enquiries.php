<?php
defined('ADMIN_PAGE') || exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $id = (string) ($_POST['id'] ?? '');
    $list = enquiries_all();
    $found = false;

    if ($action === 'toggle' || $action === 'delete') {
        foreach ($list as $i => $q) {
            if (is_array($q) && ($q['id'] ?? '') === $id) {
                $found = true;
                if ($action === 'toggle') {
                    $list[$i]['read'] = empty($q['read']);
                } else {
                    array_splice($list, $i, 1);
                }
                break;
            }
        }
        if (!$found) {
            flash('error', 'That enquiry no longer exists.');
        } elseif (enquiries_save($list)) {
            flash('ok', $action === 'delete' ? 'Enquiry deleted.' : 'Enquiry updated.');
        } else {
            flash('error', 'Could not save. Check that the data folder is writable.');
        }
    } elseif ($action === 'read_all') {
        foreach ($list as $i => $q) {
            if (is_array($q)) {
                $list[$i]['read'] = true;
            }
        }
        enquiries_save($list) ? flash('ok', 'All enquiries marked as read.') : flash('error', 'Could not save.');
    }
    redirect_to('enquiries');
}

$list = array_values(array_filter(enquiries_all(), 'is_array'));
usort($list, fn($a, $b) => strcmp((string) ($b['created'] ?? ''), (string) ($a['created'] ?? '')));
$unread = count(array_filter($list, fn($q) => empty($q['read'])));

admin_head('Enquiries', 'enquiries');
page_title('Enquiries', 'Messages sent from the contact form on the website, newest first.');
?>
<?php if (!$list): ?>
  <section class="card"><p class="muted">No enquiries yet. They will appear here as soon as someone fills in the contact form.</p></section>
<?php else: ?>
  <?php if ($unread): ?>
  <form method="post" action="<?= e(admin_url('enquiries')) ?>" class="bar">
    <?= csrf_field() ?><input type="hidden" name="action" value="read_all">
    <span><b><?= $unread ?></b> unread</span>
    <button class="btn btn-sm" type="submit">Mark all as read</button>
  </form>
  <?php endif; ?>
  <?php foreach ($list as $q):
    $id = (string) ($q['id'] ?? '');
    $isNew = empty($q['read']);
    $ts = strtotime((string) ($q['created'] ?? '')) ?: 0; ?>
  <article class="card enq<?= $isNew ? ' enq-new' : '' ?>">
    <header class="enq-head">
      <div>
        <h2><?= e((string) ($q['name'] ?? 'Unknown')) ?> <?php if ($isNew): ?><span class="badge badge-gold">New</span><?php endif; ?></h2>
        <span class="muted small"><?= $ts ? e(date('j M Y, g:i a', $ts)) : '' ?></span>
      </div>
      <div class="enq-actions">
        <form method="post" action="<?= e(admin_url('enquiries')) ?>" class="inline">
          <?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= e($id) ?>">
          <button class="btn btn-sm" type="submit"><?= $isNew ? 'Mark as read' : 'Mark as unread' ?></button>
        </form>
        <form method="post" action="<?= e(admin_url('enquiries')) ?>" class="inline" data-confirm="Delete this enquiry? This cannot be undone.">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($id) ?>">
          <button class="btn btn-sm btn-danger" type="submit">Delete</button>
        </form>
      </div>
    </header>
    <dl class="enq-meta">
      <?php if (!empty($q['phone'])): ?><div><dt>Phone</dt><dd><a href="<?= e(tel_link((string) $q['phone'])) ?>"><?= e((string) $q['phone']) ?></a></dd></div><?php endif; ?>
      <?php if (!empty($q['email'])): ?><div><dt>Email</dt><dd><a href="mailto:<?= e((string) $q['email']) ?>"><?= e((string) $q['email']) ?></a></dd></div><?php endif; ?>
      <?php if (!empty($q['location'])): ?><div><dt>Location</dt><dd><?= e((string) $q['location']) ?></dd></div><?php endif; ?>
      <?php if (!empty($q['service'])): ?><div><dt>Interested in</dt><dd><?= e((string) $q['service']) ?></dd></div><?php endif; ?>
    </dl>
    <p class="enq-msg"><?= nl2br(e((string) ($q['message'] ?? ''))) ?></p>
  </article>
  <?php endforeach; ?>
<?php endif; ?>
<?php admin_foot();
