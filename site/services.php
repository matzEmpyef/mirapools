<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';

$crumbs = [['Services', '/services']];
$list = [];
$n = 0;
foreach (services() as $slug => $svc) {
    $list[] = ['@type' => 'ListItem', 'position' => ++$n, 'url' => abs_url('/services/' . $slug), 'name' => $svc['name']];
}
$page = [
    'title'       => 'Swimming Pool, Spa, Fountain & Water Treatment Services in Kerala',
    'description' => 'Swimming pool construction, pool and spa filtration, jacuzzis, saunas, fountains, water treatment plants, STP/ETP, UV and ultrafiltration, fish pond filtration, maintenance contracts and spares across Kerala.',
    'path'        => '/services',
    'image'       => img('services_hero'),
    'schema'      => [breadcrumb_schema($crumbs), ['@context' => 'https://schema.org', '@type' => 'ItemList', 'itemListElement' => $list]],
    'body'        => 'has-dark-hero',
];
require __DIR__ . '/includes/header.php';

page_hero('Everything your water <em>needs.</em>', 'Our services', 'Ten services, from the first pool sketch to the spare part you need years later. Pick one to see what is included.', 'services_hero', $crumbs);
?>

<section class="section">
  <div class="wrap">
    <div class="svc-grid">
      <?php $i = 0; foreach (services() as $slug => $svc): $i++; ?>
        <a class="svc-card" href="/services/<?= e($slug) ?>" data-reveal style="--d:<?= ($i % 3) * 0.08 ?>s">
          <figure>
            <div><?= picture(img_thumb('svc_' . $slug), $svc['name'], ['sizes' => '(min-width: 900px) 33vw, 100vw']) ?></div>
            <span class="svc-card-icon"><?= service_icon($slug) ?></span>
          </figure>
          <div class="svc-card-body">
            <span class="num"><?= str_pad((string) $i, 2, '0', STR_PAD_LEFT) ?></span>
            <h2 class="h3-like"><?= e($svc['name']) ?></h2>
            <p><?= e($svc['short']) ?></p>
            <span class="link-arrow">See details <?= arrow() ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section bg-deep" style="overflow:hidden">
  <div class="tiles-bg" aria-hidden="true"></div>
  <div class="wrap" style="position:relative">
    <div class="section-head">
      <p class="eyebrow eyebrow-light" data-reveal>Who we work with</p>
      <h2 class="display h2" data-split>Homes, hotels and <em>everyone</em> in between.</h2>
    </div>
    <ul class="audience">
      <?php
      $aud = [
          ['Homeowners', 'Villa and home pools, spas and garden features.'],
          ['Resorts & hotels', 'Pools, spas and water systems that run every day.'],
          ['Builders', 'Pools and treatment plants for apartment and villa projects.'],
          ['Architects', 'Design support and reliable delivery of your vision.'],
          ['Pool contractors', 'Filtration, equipment, spares and chemicals.'],
      ];
      foreach ($aud as $k => [$t, $d]): ?>
        <li data-reveal style="--d:<?= $k * 0.07 ?>s"><strong><?= e($t) ?></strong><span><?= e($d) ?></span></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php cta_band('Not sure which service you need?', 'Describe the problem or the project. We will point you to the right solution, even if it is a small one.'); ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
