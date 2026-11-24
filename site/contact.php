<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';

// The form token lives in the session, which must start before any output.
start_session();
csrf_token();

$s = all_settings();
$services = services();
$errors = [];
$old = ['name' => '', 'phone' => '', 'email' => '', 'location' => '', 'service' => (string) ($_GET['service'] ?? ''), 'message' => ''];
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $_) {
        $old[$k] = trim((string) ($_POST[$k] ?? ''));
    }
    if (!csrf_check()) {
        $formError = 'Your session expired. Please send the form again.';
    } elseif (!empty($_POST['website'])) {
        // honeypot filled in: quietly pretend it worked
        header('Location: /contact?sent=1#enquiry', true, 303);
        exit;
    } else {
        if (mb_strlen($old['name']) < 2) $errors['name'] = 'Please enter your name.';
        if (!preg_match('/^[0-9+()\-\s]{8,20}$/', $old['phone'])) $errors['phone'] = 'Please enter a valid phone number.';
        if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'This e-mail address does not look right.';
        if ($old['service'] !== '' && $old['service'] !== 'other' && !isset($services[$old['service']])) $old['service'] = '';
        if (mb_strlen($old['message']) > 3000) $errors['message'] = 'Please keep the message under 3000 characters.';

        if (!$errors) {
            if (!rate_limit('enquiry', 5, 3600)) {
                $formError = 'Too many enquiries from this connection. Please call or WhatsApp us instead.';
            } else {
                $entry = [
                    'id'       => 'e' . bin2hex(random_bytes(5)),
                    'name'     => mb_substr($old['name'], 0, 120),
                    'phone'    => mb_substr($old['phone'], 0, 20),
                    'email'    => mb_substr($old['email'], 0, 160),
                    'location' => mb_substr($old['location'], 0, 120),
                    'service'  => $old['service'] === 'other' ? 'Other' : ($services[$old['service']]['name'] ?? ''),
                    'message'  => $old['message'],
                    'created'  => date('c'),
                    'read'     => false,
                    'ip'       => client_ip(),
                ];
                $list = enquiries_all();
                array_unshift($list, $entry);
                enquiries_save($list);

                if (ENQUIRY_MAIL_TO !== '' && function_exists('mail')) {
                    $body = "New enquiry from the website\n\n"
                        . "Name: {$entry['name']}\nPhone: {$entry['phone']}\nE-mail: {$entry['email']}\n"
                        . "Location: {$entry['location']}\nService: {$entry['service']}\n\n{$entry['message']}\n";
                    // Send from the receiving mailbox itself: shared hosts often drop mail from addresses that don't exist.
                    $headers = 'From: Mira Pools website <' . ENQUIRY_MAIL_TO . ">\r\nContent-Type: text/plain; charset=UTF-8";
                    if ($entry['email'] !== '') {
                        $headers .= "\r\nReply-To: " . str_replace(["\r", "\n"], '', $entry['email']);
                    }
                    @mail(ENQUIRY_MAIL_TO, 'Website enquiry: ' . str_replace(["\r", "\n"], ' ', $entry['name']), $body, $headers);
                }
                header('Location: /contact?sent=1#enquiry', true, 303);
                exit;
            }
        }
    }
}

$sent = isset($_GET['sent']);
$crumbs = [['Contact', '/contact']];
$page = [
    'title'       => 'Contact Mira Pools – Kochi, Kozhikode, Kasaragod, Thiruvananthapuram',
    'description' => 'Call, WhatsApp or send an enquiry to Mira Pools. Head office in Kakkanad, Kochi, with offices in Kozhikode, Kasaragod and Thiruvananthapuram.',
    'path'        => '/contact',
    'image'       => img('contact_hero'),
    'schema'      => [breadcrumb_schema($crumbs)],
    'body'        => 'has-dark-hero',
];
require __DIR__ . '/includes/header.php';

page_hero("Let's talk about your <em>water.</em>", 'Contact us', 'Call, WhatsApp or send us a few details. We will get back to you soon.', 'contact_hero', $crumbs);

$field = function (string $name) use ($errors): string {
    return isset($errors[$name]) ? ' has-err' : '';
};
?>

<section class="section">
  <div class="wrap contact-grid">
    <div>
      <p class="eyebrow" data-reveal><span class="num">01</span> Reach us</p>
      <h2 class="display h2" data-split>The quickest way is a <em>call.</em></h2>
      <p data-reveal style="color:var(--muted)">Or drop in at our head office in Kakkanad. For service calls, the office nearest to you can usually reach you fastest.</p>

      <div class="contact-cards">
        <a class="c-card" href="<?= e(tel_link($s['phone'])) ?>" data-reveal>
          <span class="c-ic"><?= icon('phone') ?></span><span><small>Call</small><strong><?= e($s['phone']) ?></strong></span>
        </a>
        <a class="c-card c-card-wa" href="<?= e(wa_link('Hello Mira Pools, ')) ?>" target="_blank" rel="noopener" data-reveal>
          <span class="c-ic"><?= icon('chat') ?></span><span><small>WhatsApp</small><strong>Chat with us</strong></span>
        </a>
        <div class="c-card" data-reveal>
          <span class="c-ic"><?= icon('mail') ?></span>
          <span><small>E-mail</small>
            <?php foreach (array_filter([$s['email'], $s['email_alt']]) as $mail): ?>
              <a class="c-mail" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a>
            <?php endforeach; ?>
          </span>
        </div>
        <div class="c-card" data-reveal>
          <span class="c-ic"><?= icon('pin') ?></span><span><small>Head office</small><strong><?= e($s['address_line']) ?>, <?= e($s['locality']) ?>, <?= e($s['city']) ?> <?= e($s['postcode']) ?></strong></span>
        </div>
        <?php if ($s['hours']): ?>
          <div class="c-card" data-reveal>
            <span class="c-ic"><?= icon('clock') ?></span><span><small>Office hours</small><strong><?= e($s['hours']) ?></strong></span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="form-card" id="enquiry" data-reveal>
      <?php if ($sent): ?>
        <div class="sent">
          <div class="sent-ic"><?= icon('check') ?></div>
          <h2>Thank you!</h2>
          <p>Your enquiry has reached us. Our team will contact you soon. If it is urgent, please call <a href="<?= e(tel_link($s['phone'])) ?>"><?= e($s['phone']) ?></a>.</p>
          <a class="link-arrow" href="/">Back to the home page <?= arrow() ?></a>
        </div>
      <?php else: ?>
        <h2>Send an enquiry</h2>
        <p>A few details help us prepare. Fields marked optional can be left blank.</p>
        <?php if ($formError): ?><div class="alert alert-err" role="alert"><?= e($formError) ?></div><?php endif; ?>
        <form method="post" action="/contact#enquiry" novalidate>
          <?= csrf_field() ?>
          <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
          <div class="form-grid">
            <div class="field<?= $field('name') ?>">
              <label for="f-name">Your name</label>
              <input id="f-name" name="name" type="text" autocomplete="name" required value="<?= e($old['name']) ?>">
              <?php if (isset($errors['name'])): ?><span class="err"><?= e($errors['name']) ?></span><?php endif; ?>
            </div>
            <div class="field<?= $field('phone') ?>">
              <label for="f-phone">Phone / WhatsApp</label>
              <input id="f-phone" name="phone" type="tel" autocomplete="tel" inputmode="tel" required value="<?= e($old['phone']) ?>">
              <?php if (isset($errors['phone'])): ?><span class="err"><?= e($errors['phone']) ?></span><?php endif; ?>
            </div>
            <div class="field<?= $field('email') ?>">
              <label for="f-email">E-mail <span>(optional)</span></label>
              <input id="f-email" name="email" type="email" autocomplete="email" value="<?= e($old['email']) ?>">
              <?php if (isset($errors['email'])): ?><span class="err"><?= e($errors['email']) ?></span><?php endif; ?>
            </div>
            <div class="field">
              <label for="f-loc">Town or district <span>(optional)</span></label>
              <input id="f-loc" name="location" type="text" autocomplete="address-level2" placeholder="e.g. Thrissur" value="<?= e($old['location']) ?>">
            </div>
            <div class="field field-full">
              <label for="f-svc">What do you need?</label>
              <select id="f-svc" name="service">
                <option value="">Choose a service</option>
                <?php foreach ($services as $slug => $svc): ?>
                  <option value="<?= e($slug) ?>"<?= $old['service'] === $slug ? ' selected' : '' ?>><?= e($svc['name']) ?></option>
                <?php endforeach; ?>
                <option value="other"<?= $old['service'] === 'other' ? ' selected' : '' ?>>Something else</option>
              </select>
            </div>
            <div class="field field-full<?= $field('message') ?>">
              <label for="f-msg">Tell us a little more <span>(optional)</span></label>
              <textarea id="f-msg" name="message" placeholder="Size of the pool, type of property, the problem you are facing..."><?= e($old['message']) ?></textarea>
              <?php if (isset($errors['message'])): ?><span class="err"><?= e($errors['message']) ?></span><?php endif; ?>
            </div>
          </div>
          <div class="form-foot">
            <small>We only use your details to reply to this enquiry.</small>
            <button class="btn btn-teal" type="submit">Send enquiry <?= arrow() ?></button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section bg-sand" style="padding-block:90px">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow" data-reveal><span class="num">02</span> Our offices</p>
      <h2 class="display h2" data-split>Four offices, <em>one</em> standard.</h2>
    </div>
    <div class="offices" style="margin-bottom:40px">
      <?php foreach ($s['branches'] as $k => $b): $hq = stripos($b['city'], 'head') !== false; ?>
        <a class="office<?= $hq ? ' office-hq' : '' ?>" href="<?= e(tel_link($b['phone'])) ?>" data-reveal style="--d:<?= $k * 0.08 ?>s">
          <?= icon('pin') ?>
          <strong><?= e(preg_replace('/\s*\(.*\)/', '', $b['city'])) ?></strong>
          <small><?= $hq ? 'Head office · ' : '' ?><?= e($b['district']) ?></small>
          <span><?= e($b['phone']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <div class="map" data-map="https://maps.google.com/maps?q=<?= e(rawurlencode($s['map_query'])) ?>&amp;z=15&amp;output=embed" data-reveal>
      <div class="map-load">
        <p style="margin:0;color:var(--muted)">Head office: <?= e($s['locality']) ?>, <?= e($s['city']) ?></p>
        <button class="btn btn-ghost" type="button"><?= icon('pin') ?> Show map</button>
        <a class="link-arrow" href="https://www.google.com/maps/search/?api=1&amp;query=<?= e(rawurlencode($s['map_query'])) ?>" target="_blank" rel="noopener">Open in Google Maps <?= arrow() ?></a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
