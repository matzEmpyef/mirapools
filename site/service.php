<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';

$slug = (string) ($_GET['slug'] ?? '');
$svc = service($slug);
if (!$svc) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$crumbs = [['Services', '/services'], [$svc['name'], '/services/' . $slug]];
$faqLd = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(fn($f) => ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]], $svc['faqs']),
];
$serviceLd = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Service',
    'name'        => $svc['name'],
    'description' => $svc['lead'],
    'serviceType' => $svc['name'],
    'provider'    => ['@id' => abs_url('/#business')],
    'areaServed'  => ['@type' => 'State', 'name' => 'Kerala'],
    'url'         => abs_url('/services/' . $slug),
];
$page = [
    'title'       => $svc['name'] . ' in Kerala',
    'description' => $svc['lead'],
    'path'        => '/services/' . $slug,
    'image'       => img('svc_' . $slug),
    'schema'      => [breadcrumb_schema($crumbs), $serviceLd, $faqLd],
    'body'        => 'has-dark-hero',
];
require __DIR__ . '/includes/header.php';

$words = explode(' ', $svc['name']);
$last = array_pop($words);
$title = e(implode(' ', $words)) . ($words ? ' ' : '') . '<em>' . e($last) . '</em>';
page_hero($title, 'Service', $svc['short'], 'svc_' . $slug, $crumbs);

$related = array_values(array_filter(gallery_all(), fn($g) => $g['category'] === service_gallery_category($slug)));
$related = array_slice($related, 0, 4);
?>

<section class="section">
  <div class="wrap svc-detail">
    <article class="prose">
      <p class="lead" data-reveal><?= e($svc['lead']) ?></p>
      <?php foreach ($svc['body'] as $p): ?>
        <p data-reveal><?= e($p) ?></p>
      <?php endforeach; ?>

      <div class="svc-includes" data-reveal>
        <h2>What's included</h2>
        <ul class="check-list cols-2">
          <?php foreach ($svc['includes'] as $x): ?>
            <li><?= icon('check') ?> <span><?= e($x) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <?php if ($related): ?>
        <h2 class="display" style="font-size:1.8rem;margin:10px 0 18px" data-reveal>From our gallery</h2>
        <div class="related-grid" data-reveal>
          <?php foreach ($related as $g): ?>
            <a href="/gallery#<?= e($g['id']) ?>"><?= picture($g['thumb'] ?: $g['src'], $g['alt'] ?: $g['title'], ['sizes' => '(min-width: 900px) 15vw, 45vw']) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <h2 class="display" style="font-size:1.8rem;margin:56px 0 8px" data-reveal>Common questions</h2>
      <div class="faq" data-reveal>
        <?php foreach ($svc['faqs'] as [$q, $a]): ?>
          <details><summary><?= e($q) ?></summary><p><?= e($a) ?></p></details>
        <?php endforeach; ?>
      </div>
    </article>

    <aside class="svc-aside">
      <div class="aside-card" data-reveal>
        <h2>Ideal for</h2>
        <ul class="tag-list">
          <?php foreach ($svc['suited'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?>
        </ul>
      </div>
      <div class="aside-card aside-card-dark" data-reveal style="--d:.1s">
        <h2>Get a quote</h2>
        <p>Tell us about your project. We usually start with a site visit.</p>
        <a class="btn btn-sun" href="/contact?service=<?= e($slug) ?>">Send an enquiry <?= arrow() ?></a>
        <a class="btn btn-ghost-light" href="<?= e(wa_link('Hello Mira Pools, I am interested in ' . strtolower($svc['name']) . '.')) ?>" target="_blank" rel="noopener"><?= icon('chat') ?> WhatsApp</a>
        <a class="btn btn-ghost-light" href="<?= e(tel_link(settings('phone'))) ?>"><?= icon('phone') ?> <?= e(settings('phone')) ?></a>
      </div>
    </aside>
  </div>
</section>

<section class="section bg-sand" style="padding-block:80px">
  <div class="wrap">
    <p class="eyebrow" data-reveal>Other services</p>
    <nav class="svc-nav" aria-label="Other services">
      <?php foreach (services() as $s2 => $o): if ($s2 === $slug) continue; ?>
        <a href="/services/<?= e($s2) ?>"><?= service_icon($s2) ?><?= e($o['menu']) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</section>

<?php cta_band(); ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
