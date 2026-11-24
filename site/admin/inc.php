<?php
/**
 * Admin bootstrap: auth, flash messages, helpers and page layout.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/functions.php';

const ADMIN_IDLE_SECONDS = 7200;

/* ---------- small helpers ---------- */

function admin_base(): string
{
    $dir = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php'))), '/');
    return $dir === '' ? '/admin' : $dir;
}

function admin_url(string $page = 'dashboard', array $extra = []): string
{
    return admin_base() . '/?' . http_build_query(['p' => $page] + $extra);
}

function redirect_to(string $page, array $extra = []): never
{
    header('Location: ' . admin_url($page, $extra));
    exit;
}

function flash(string $type, string $msg): void
{
    start_session();
    $_SESSION['flash'][] = [$type, $msg];
}

function flash_take(): array
{
    start_session();
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function is_ajax(): bool
{
    return ($_POST['ajax'] ?? '') === '1';
}

function json_reply(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function now_iso(): string
{
    return date('c');
}

/* ---------- authentication ---------- */

function admin_record(): array
{
    return json_read('admin', []);
}

function admin_is_setup(): bool
{
    return !empty(admin_record()['password_hash']);
}

function admin_logged_in(): bool
{
    start_session();
    if (empty($_SESSION['admin_ok'])) {
        return false;
    }
    if (time() - (int) ($_SESSION['admin_last'] ?? 0) > ADMIN_IDLE_SECONDS) {
        unset($_SESSION['admin_ok'], $_SESSION['admin_last']);
        flash('error', 'You were signed out after 2 hours without activity. Please sign in again.');
        return false;
    }
    $_SESSION['admin_last'] = time();
    return true;
}

function admin_store_password(string $plain): bool
{
    return json_write('admin', [
        'password_hash' => password_hash($plain, PASSWORD_DEFAULT),
        'updated'       => now_iso(),
    ]);
}

function admin_check_password(string $plain): bool
{
    $hash = (string) (admin_record()['password_hash'] ?? '');
    return $hash !== '' && password_verify($plain, $hash);
}

/* ---------- data helpers ---------- */

function unread_enquiries(): int
{
    return count(array_filter(enquiries_all(), fn($q) => is_array($q) && empty($q['read'])));
}

function admin_nav(): array
{
    return [
        'dashboard' => ['Dashboard', 'M3 12l9-8 9 8M5 10v10h5v-6h4v6h5V10'],
        'projects'  => ['Projects', 'M9 4h6v3H9zM6 6h12v15H6zM9 11h6M9 15h4'],
        'gallery'   => ['Gallery', 'M4 5h16v14H4zM4 15l5-5 4 4 3-3 4 4M9 9.5h.01'],
        'images'    => ['Site images', 'M3 4h18v12H3zM8 20h8M12 16v4'],
        'enquiries' => ['Enquiries', 'M4 6h16v12H4zM4 7l8 6 8-6'],
        'settings'  => ['Settings', 'M12 15a3 3 0 100-6 3 3 0 000 6zM19 12l2-1-2-4-2 1-2-1-1-2H9L8 7 6 8 4 7 2 11l2 1v2l-2 1 2 4 2-1 2 1 1 2h4l1-2 2-1 2 1 2-4-2-1z'],
    ];
}

function adm_icon(string $d): string
{
    return '<svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><path d="' . e($d) . '"/></svg>';
}

/* ---------- layout ---------- */

function admin_head(string $title, string $page = ''): void
{
    $css = admin_base() . '/admin.css?v=' . (string) @filemtime(__DIR__ . '/admin.css');
    $js = admin_base() . '/admin.js?v=' . (string) @filemtime(__DIR__ . '/admin.js');
    $loggedIn = $page !== 'login' && $page !== 'setup';
    $GLOBALS['admin_logged_layout'] = $loggedIn;
    $GLOBALS['admin_page'] = $page;
    ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= e($title) ?> – Mira Pools admin</title>
<link rel="icon" href="/favicon.ico?v=<?= (int) @filemtime(BASE_DIR . '/favicon.ico') ?>" sizes="48x48">
<link rel="icon" href="<?= e(asset('img/favicon-32.png')) ?>" type="image/png" sizes="32x32">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e($css) ?>">
<script src="<?= e($js) ?>" defer></script>
</head>
<body class="<?= $loggedIn ? 'app' : 'plain' ?>">
<?php if ($loggedIn): $unread = unread_enquiries(); ?>
<header class="topbar">
  <button class="menu-btn" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="sidebar" data-menu-toggle>
    <span></span><span></span><span></span>
  </button>
  <span class="topbar-brand">Mira Pools admin</span>
  <?php if ($unread > 0): ?><a class="badge badge-gold" href="<?= e(admin_url('enquiries')) ?>"><?= $unread ?> new</a><?php endif; ?>
</header>
<aside class="sidebar" id="sidebar">
  <a class="brand" href="<?= e(admin_url()) ?>">
    <span class="brand-mark">M</span>
    <span><strong>Mira Pools</strong><small>Website admin</small></span>
  </a>
  <nav aria-label="Main">
    <?php foreach (admin_nav() as $key => [$label, $path]): ?>
      <a href="<?= e(admin_url($key)) ?>" class="<?= $page === $key ? 'active' : '' ?>"<?= $page === $key ? ' aria-current="page"' : '' ?>>
        <?= adm_icon($path) ?><span><?= e($label) ?></span>
        <?php if ($key === 'enquiries' && $unread > 0): ?><em class="badge badge-gold"><?= $unread ?></em><?php endif; ?>
      </a>
    <?php endforeach; ?>
  </nav>
  <div class="side-foot">
    <a href="/" target="_blank" rel="noopener" class="side-link"><?= adm_icon('M14 4h6v6M20 4l-9 9M18 14v6H4V6h6') ?><span>View live site</span></a>
    <button type="button" class="side-link" data-modal-open="pw-modal"><?= adm_icon('M7 11V8a5 5 0 0110 0v3M5 11h14v10H5zM12 15v2') ?><span>Change password</span></button>
    <form method="post" action="<?= e(admin_url('logout')) ?>">
      <?= csrf_field() ?>
      <button class="side-link" type="submit"><?= adm_icon('M15 4h4v16h-4M10 8l-4 4 4 4M6 12h10') ?><span>Sign out</span></button>
    </form>
  </div>
</aside>
<div class="scrim" data-menu-toggle></div>
<main class="main" id="main">
<?php else: ?>
<main class="auth-wrap">
<?php endif;
    foreach (flash_take() as [$type, $msg]) {
        echo '<div class="flash flash-' . e($type) . '" role="' . ($type === 'error' ? 'alert' : 'status') . '">' . e($msg) . '</div>';
    }
}

function admin_foot(): void
{
    echo "\n</main>\n";
    if (!empty($GLOBALS['admin_logged_layout'])) {
        admin_shared_modals();
    }
    echo "</body>\n</html>\n";
}

/** Popup frame. Call modal_open(), print the body, then modal_close(). */
function modal_open(string $id, string $title, string $sub = '', string $size = ''): void
{
    ?>
<dialog class="modal<?= $size ? ' modal-' . e($size) : '' ?>" id="<?= e($id) ?>" aria-labelledby="<?= e($id) ?>-title">
  <div class="modal-head">
    <div>
      <h2 id="<?= e($id) ?>-title"><?= e($title) ?></h2>
      <?php if ($sub !== ''): ?><p class="muted small"><?= e($sub) ?></p><?php endif; ?>
    </div>
    <button type="button" class="modal-x" data-modal-close aria-label="Close">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
  </div>
  <div class="modal-body">
    <?php
}

function modal_close(): void
{
    echo "  </div>\n</dialog>\n";
}

/** Popups available on every signed-in page: change password and the confirm prompt. */
function admin_shared_modals(): void
{
    modal_open('pw-modal', 'Change password', 'Use at least 10 characters. You stay signed in.', 'sm');
    ?>
    <form method="post" action="<?= e(admin_url('settings')) ?>" autocomplete="off">
      <?= csrf_field() ?><input type="hidden" name="action" value="password">
      <input type="hidden" name="return" value="<?= e((string) ($GLOBALS['admin_page'] ?? 'dashboard')) ?>">
      <label class="field"><span>Current password</span><input type="password" name="current" required autocomplete="current-password"></label>
      <label class="field"><span>New password</span><input type="password" name="new" required minlength="10" autocomplete="new-password"></label>
      <label class="field"><span>New password again</span><input type="password" name="new2" required minlength="10" autocomplete="new-password"></label>
      <div class="modal-foot">
        <button type="button" class="btn" data-modal-close>Cancel</button>
        <button class="btn btn-primary" type="submit">Change password</button>
      </div>
    </form>
    <?php
    modal_close();
    ?>
<dialog class="modal modal-sm modal-confirm" id="confirm-modal" aria-labelledby="confirm-title">
  <div class="modal-body">
    <div class="confirm-ic" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13M10 11v6M14 11v6"/></svg></div>
    <h2 id="confirm-title" data-confirm-text>Are you sure?</h2>
    <p class="muted small">This cannot be undone.</p>
    <div class="modal-foot">
      <button type="button" class="btn" data-modal-close>Cancel</button>
      <button type="button" class="btn btn-danger-solid" data-confirm-yes>Delete</button>
    </div>
  </div>
</dialog>
    <?php
}

function page_title(string $title, string $sub = ''): void
{
    echo '<div class="page-head"><h1>' . e($title) . '</h1>';
    if ($sub !== '') {
        echo '<p class="muted">' . e($sub) . '</p>';
    }
    echo '</div>';
}

/** Turn $_FILES['name'] (multi-file layout) into a list of single-file arrays. */
function normalise_files(string $field): array
{
    $out = [];
    $f = $_FILES[$field] ?? null;
    if (!$f) {
        return [];
    }
    if (!is_array($f['name'] ?? null)) {
        return (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) ? [] : [$f];
    }
    foreach ($f['name'] as $i => $name) {
        if (($f['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $out[] = [
            'name'     => (string) $name,
            'type'     => (string) ($f['type'][$i] ?? ''),
            'tmp_name' => (string) ($f['tmp_name'][$i] ?? ''),
            'error'    => (int) ($f['error'][$i] ?? UPLOAD_ERR_NO_FILE),
            'size'     => (int) ($f['size'][$i] ?? 0),
        ];
    }
    return $out;
}

/** Clean one line of text from a form field. */
function clean_text(mixed $v, int $max = 200): string
{
    $v = is_string($v) ? $v : '';
    $v = trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $v) ?? '');
    return mb_substr($v, 0, $max);
}

/** Plain-English file name for error messages (never used on disk). */
function safe_label(string $name): string
{
    return mb_substr(preg_replace('/[^\p{L}\p{N}._ -]+/u', '', basename($name)) ?? '', 0, 60);
}
