<?php
defined('ADMIN_PAGE') || exit;

if (admin_logged_in()) {
    redirect_to('dashboard');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!rate_limit('login', 5, 900)) {
        flash('error', 'Too many sign-in attempts. Please wait 15 minutes and try again.');
        redirect_to('login');
    }
    if (admin_check_password((string) ($_POST['password'] ?? ''))) {
        session_regenerate_id(true);
        unset($_SESSION['csrf']);
        $_SESSION['admin_ok'] = true;
        $_SESSION['admin_last'] = time();
        redirect_to('dashboard');
    }
    flash('error', 'That password is not correct.');
    redirect_to('login');
}

admin_head('Sign in', 'login');
?>
<div class="auth-card">
  <div class="brand-mark big">M</div>
  <h1>Sign in</h1>
  <p class="muted">Mira Pools website admin</p>
  <form method="post">
    <?= csrf_field() ?>
    <label class="field"><span>Password</span>
      <input type="password" name="password" required autofocus autocomplete="current-password"></label>
    <button class="btn btn-primary btn-block" type="submit">Sign in</button>
  </form>
  <p class="muted small center"><a href="/">Back to the website</a></p>
</div>
<?php admin_foot();
