<?php
declare(strict_types=1);
$s = all_settings();
?>
</main>

<footer class="site-footer">
  <div class="footer-wave" aria-hidden="true">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none"><path d="M0 40c180-30 360-30 540 0s360 30 540 0 270-30 360-15V80H0Z"/></svg>
  </div>
  <div class="wrap footer-grid">
    <div class="footer-about">
      <a class="brand brand-footer" href="/" aria-label="Mira Pools – home"><?= logo_svg('light') ?></a>
      <p>Swimming pools, spas, fountains and water treatment, designed, built and looked after across Kerala.</p>
      <a class="footer-wa" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener"><?= icon('chat') ?> Chat on WhatsApp</a>
    </div>

    <div>
      <h2 class="footer-title">Explore</h2>
      <ul class="footer-links">
        <?php foreach (nav_pages() as $href => $label): ?>
          <li><a href="<?= $href ?>"><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div>
      <h2 class="footer-title">Services</h2>
      <ul class="footer-links">
        <?php foreach (services() as $slug => $svc): ?>
          <li><a href="/services/<?= e($slug) ?>"><?= e($svc['menu']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div>
      <h2 class="footer-title">Head office</h2>
      <address class="footer-address">
        <?= e($s['company']) ?><br>
        <?= e($s['address_line']) ?>,<br>
        <?= e($s['locality']) ?>, <?= e($s['city']) ?>, <?= e($s['region']) ?> <?= e($s['postcode']) ?>
      </address>
      <ul class="footer-links footer-contact">
        <li><a href="<?= e(tel_link($s['phone'])) ?>"><?= icon('phone') ?><?= e($s['phone']) ?></a></li>
        <?php if ($s['landline']): ?><li><a href="<?= e(tel_link($s['landline'])) ?>"><?= icon('phone') ?><?= e($s['landline']) ?></a></li><?php endif; ?>
        <?php foreach (array_filter([$s['email'], $s['email_alt']]) as $mail): ?>
          <li><a href="mailto:<?= e($mail) ?>"><?= icon('mail') ?><?= e($mail) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <div class="wrap footer-branches">
    <span class="footer-branches-label">Offices</span>
    <?php foreach ($s['branches'] as $b): ?>
      <a href="<?= e(tel_link($b['phone'])) ?>"><strong><?= e(preg_replace('/\s*\(.*\)/', '', $b['city'])) ?></strong> <?= e($b['phone']) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="wrap footer-bottom">
    <p>© <?= date('Y') ?> <?= e($s['company']) ?>. All rights reserved.</p>
    <?php $social = array_filter(['Facebook' => $s['facebook'], 'Instagram' => $s['instagram'], 'YouTube' => $s['youtube']]); ?>
    <?php if ($social): ?>
      <p class="footer-social"><?php foreach ($social as $name => $url): ?><a href="<?= e($url) ?>" target="_blank" rel="noopener"><?= e($name) ?></a><?php endforeach; ?></p>
    <?php endif; ?>
    <p class="powered">Powered by <a href="http://empyef.com/" target="_blank" rel="noopener">Empyef Technologies</a></p>
  </div>
</footer>

<a class="wa-float" href="<?= e(wa_link('Hello Mira Pools, ')) ?>" target="_blank" rel="noopener" aria-label="Chat with Mira Pools on WhatsApp">
  <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16 3a13 13 0 0 0-11.2 19.6L3 29l6.6-1.7A13 13 0 1 0 16 3Zm0 23.7c-2 0-4-.5-5.7-1.6l-.4-.2-3.9 1 1-3.8-.3-.4A10.7 10.7 0 1 1 16 26.7Zm5.9-8c-.3-.2-1.9-1-2.2-1s-.5-.2-.7.2-.8 1-1 1.2-.4.2-.7 0a8.7 8.7 0 0 1-4.3-3.8c-.3-.6.3-.5.9-1.7.1-.2 0-.4 0-.5l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6 0-.9.4s-1.1 1.1-1.1 2.7 1.2 3.2 1.3 3.4 2.3 3.5 5.6 4.9c2.1.9 2.9 1 4 .8.6-.1 1.9-.8 2.2-1.5s.3-1.4.2-1.5-.3-.2-.6-.4Z"/></svg>
</a>

<script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>
