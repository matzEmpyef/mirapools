<?php
defined('ADMIN_PAGE') || exit;

const MAX_BRANCHES = 8;

$labels = [
    'company'      => ['Company name', ''],
    'brand'        => ['Brand name', 'Shown in the website header and footer.'],
    'tagline'      => ['Tagline', 'One line under the brand name.'],
    'phone'        => ['Main phone', ''],
    'phone_alt'    => ['Second phone', ''],
    'landline'     => ['Landline', ''],
    'whatsapp'     => ['WhatsApp number', 'Digits only with country code, e.g. 919995855133'],
    'email'        => ['Main email', ''],
    'email_alt'    => ['Second email', ''],
    'address_line' => ['Street address', ''],
    'locality'     => ['Area', 'e.g. Kakkanad'],
    'city'         => ['City', ''],
    'region'       => ['State', ''],
    'postcode'     => ['PIN code', ''],
    'hours'        => ['Opening hours', 'e.g. Mon–Sat, 9 am to 6 pm. Leave empty to hide.'],
    'ceo'          => ['Managing director', 'Name shown on the About page.'],
    'map_query'    => ['Map search text', 'What the website looks up on Google Maps.'],
    'facebook'     => ['Facebook page', 'Full web address. Leave empty to hide.'],
    'instagram'    => ['Instagram page', 'Full web address. Leave empty to hide.'],
    'youtube'      => ['YouTube channel', 'Full web address. Leave empty to hide.'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save') {
        $out = [];
        foreach (default_settings() as $key => $default) {
            if ($key === 'branches') {
                continue;
            }
            $v = clean_text($_POST[$key] ?? '', 300);
            if ($key === 'whatsapp') {
                $v = preg_replace('/\D+/', '', $v) ?? '';
            }
            if (in_array($key, ['facebook', 'instagram', 'youtube'], true) && $v !== '' && !preg_match('#^https?://#i', $v)) {
                $v = 'https://' . $v;
            }
            $out[$key] = $v;
        }
        $branches = [];
        $rows = $_POST['branches'] ?? [];
        if (is_array($rows)) {
            foreach ($rows as $r) {
                if (!is_array($r)) {
                    continue;
                }
                $b = [
                    'city'     => clean_text($r['city'] ?? '', 80),
                    'district' => clean_text($r['district'] ?? '', 80),
                    'phone'    => clean_text($r['phone'] ?? '', 40),
                ];
                if ($b['city'] === '' && $b['district'] === '' && $b['phone'] === '') {
                    continue;
                }
                $branches[] = $b;
                if (count($branches) >= MAX_BRANCHES) {
                    break;
                }
            }
        }
        $out['branches'] = $branches;
        json_write('settings', $out) ? flash('ok', 'Settings saved.') : flash('error', 'Could not save. Check that the data folder is writable.');
        redirect_to('settings');
    }

    if ($action === 'password') {
        if (!rate_limit('pwchange', 5, 900)) {
            flash('error', 'Too many attempts. Please wait 15 minutes and try again.');
        } else {
            $cur = (string) ($_POST['current'] ?? '');
            $n1 = (string) ($_POST['new'] ?? '');
            $n2 = (string) ($_POST['new2'] ?? '');
            if (!admin_check_password($cur)) {
                flash('error', 'Your current password is not correct.');
            } elseif (mb_strlen($n1) < 10) {
                flash('error', 'The new password must be at least 10 characters.');
            } elseif ($n1 !== $n2) {
                flash('error', 'The two new passwords do not match.');
            } elseif (!admin_store_password($n1)) {
                flash('error', 'Could not save the password. Check that the data folder is writable.');
            } else {
                session_regenerate_id(true);
                flash('ok', 'Password changed.');
            }
        }
        $return = (string) ($_POST['return'] ?? 'settings');
        redirect_to(isset(admin_nav()[$return]) ? $return : 'settings');
    }

    flash('error', 'Unknown action.');
    redirect_to('settings');
}

$s = all_settings();
$branches = is_array($s['branches'] ?? null) ? array_values($s['branches']) : [];
$branches = array_slice($branches, 0, MAX_BRANCHES);
$filled = count($branches);
while (count($branches) < MAX_BRANCHES) {
    $branches[] = ['city' => '', 'district' => '', 'phone' => ''];
}

admin_head('Settings', 'settings');
page_title('Settings', 'Contact details and addresses shown across the website.');
?>
<form method="post" action="<?= e(admin_url('settings')) ?>">
  <?= csrf_field() ?><input type="hidden" name="action" value="save">
  <section class="card">
    <h2>Company and contact details</h2>
    <div class="form-grid">
      <?php foreach ($labels as $key => [$label, $hint]): ?>
      <label class="field"><span><?= e($label) ?></span>
        <input type="text" name="<?= e($key) ?>" value="<?= e((string) ($s[$key] ?? '')) ?>" maxlength="300"<?= $key === 'whatsapp' ? ' inputmode="numeric"' : '' ?>>
        <?php if ($hint !== ''): ?><small><?= e($hint) ?></small><?php endif; ?></label>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="card">
    <h2>Branch offices</h2>
    <p class="muted small">Up to <?= MAX_BRANCHES ?> offices. Empty rows are ignored.</p>
    <div class="branches" data-branches data-filled="<?= $filled ?>">
      <?php foreach ($branches as $i => $b): ?>
      <div class="branch-row"<?= ($i > $filled) ? ' data-extra hidden' : '' ?>>
        <label class="field"><span>City</span><input type="text" name="branches[<?= $i ?>][city]" value="<?= e((string) ($b['city'] ?? '')) ?>" maxlength="80"></label>
        <label class="field"><span>District / area</span><input type="text" name="branches[<?= $i ?>][district]" value="<?= e((string) ($b['district'] ?? '')) ?>" maxlength="80"></label>
        <label class="field"><span>Phone</span><input type="text" name="branches[<?= $i ?>][phone]" value="<?= e((string) ($b['phone'] ?? '')) ?>" maxlength="40"></label>
      </div>
      <?php endforeach; ?>
      <button type="button" class="btn btn-sm" data-add-branch hidden>+ Add another office</button>
    </div>
  </section>
  <div class="savebar"><button class="btn btn-primary" type="submit">Save settings</button></div>
</form>

<section class="card narrow card-row">
  <div>
    <h2>Admin password</h2>
    <p class="muted small">The password you use to sign in here.</p>
  </div>
  <button type="button" class="btn" data-modal-open="pw-modal">Change password</button>
</section>
<?php admin_foot();
