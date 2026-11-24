<?php
/**
 * Admin entry point. Routes ?p=<page>; every POST is CSRF-checked here.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/includes/php-check.php';
require __DIR__ . '/inc.php';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
header('Content-Type: text/html; charset=utf-8');

start_session();

$allowed = ['dashboard', 'projects', 'gallery', 'images', 'enquiries', 'settings', 'login', 'setup', 'logout'];
$page = (string) ($_GET['p'] ?? 'dashboard');
if (!in_array($page, $allowed, true)) {
    $page = 'dashboard';
}
$isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

// First run: only the setup screen is reachable. Afterwards it never is.
if (!admin_is_setup()) {
    $page = 'setup';
} elseif ($page === 'setup') {
    redirect_to('login');
}

if ($page !== 'login' && $page !== 'setup' && !admin_logged_in()) {
    if ($isPost && is_ajax()) {
        json_reply(['ok' => false, 'error' => 'Please sign in again.'], 401);
    }
    redirect_to('login');
}

if ($isPost) {
    // An upload bigger than the server allows arrives with an empty $_POST.
    if (!$_POST && !$_FILES && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        flash('error', 'That upload was too big for the server. Try fewer or smaller photos at a time.');
        redirect_to($page === 'logout' ? 'dashboard' : $page);
    }
    if (!csrf_check()) {
        if (is_ajax()) {
            json_reply(['ok' => false, 'error' => 'Your session expired. Reload the page.'], 400);
        }
        flash('error', 'Your session expired. Please try again.');
        redirect_to($page === 'logout' ? 'dashboard' : $page);
    }
} elseif ($page === 'logout') {
    redirect_to('dashboard');
}

if ($page === 'logout') {
    $_SESSION = [];
    session_destroy();
    start_session();
    session_regenerate_id(true);
    flash('ok', 'You have been signed out.');
    redirect_to('login');
}

const ADMIN_PAGE = true;
require __DIR__ . '/pages/' . $page . '.php';
