<?php
defined('ADMIN_PAGE') || exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!rate_limit('setup', 10, 900)) {
        flash('error', 'Too many attempts. Please wait 15 minutes and try again.');
        redirect_to('setup');
    }
    $key = (string) ($_POST['setup_key'] ?? '');
    $p1 = (string) ($_POST['password'] ?? '');
    $p2 = (string) ($_POST['password2'] ?? '');
    if (strlen(ADMIN_SETUP_KEY) < 16) {
        flash('error', 'No setup key is set yet. Add one to ADMIN_SETUP_KEY in includes/config.php (the upload package does this for you).');
    } elseif (!hash_equals(ADMIN_SETUP_KEY, $key)) {
        flash('error', 'That setup key is not correct.');
    } elseif (mb_strlen($p1) < 10) {
        flash('error', 'Please choose a password of at least 10 characters.');
    } elseif ($p1 !== $p2) {
        flash('error', 'The two passwords do not match.');
    } elseif (!admin_store_password($p1)) {
        flash('error', 'Could not save the password. Check that the data folder is writable.');
    } else {
        flash('ok', 'Password created. You can now sign in.');
        redirect_to('login');
    }
    redirect_to('setup');
}

admin_head('First-time setup', 'setup');
?>
<div class="auth-card">
  <div class="brand-mark big">M</div>
  <h1>Welcome</h1>
  <p class="muted">Create the password you will use to manage the website. You only do this once.</p>
  <form method="post" autocomplete="off">
    <?= csrf_field() ?>
    <label class="field"><span>Setup key</span>
      <input type="password" name="setup_key" required autocomplete="off">
      <small>The private key from the website's configuration file.</small></label>
    <label class="field"><span>New password</span>
      <input type="password" name="password" required minlength="10" autocomplete="new-password">
      <small>At least 10 characters.</small></label>
    <label class="field"><span>Type the password again</span>
      <input type="password" name="password2" required minlength="10" autocomplete="new-password"></label>
    <button class="btn btn-primary btn-block" type="submit">Create password</button>
  </form>
</div>
<?php admin_foot();
