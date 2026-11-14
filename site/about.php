<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';

$s = all_settings();
$crumbs = [['About', '/about']];
$page = [
    'title'       => 'About Mira Pools – Water Management Experts in Kerala',
    'description' => 'Mira Water Management (Mira Pools) is a Kerala company offering complete water solutions: swimming pools, spas, fountains, water and sewage treatment, maintenance contracts and spares. Head office in Kakkanad, Kochi.',
    'path'        => '/about',
    'image'       => img('about_hero'),
    'schema'      => [breadcrumb_schema($crumbs)],
    'body'        => 'has-dark-hero',
];
require __DIR__ . '/includes/header.php';

page_hero('Built on <em>clear water</em> and plain dealing.', 'About Mira Pools', 'A Kerala team of swimming pool professionals and design engineers, growing through the support of the customers we already serve.', 'about_hero', $crumbs);
?>

<section class="section">
  <div class="wrap two-col">
    <div class="prose">
      <p class="eyebrow" data-reveal><span class="num">01</span> Our story</p>
      <h2 class="display h2" data-split>Quality first, <em>then</em> everything else.</h2>
      <p class="lead" data-reveal>Mira Water Management was founded to give customers the best possible experience, with quality at the centre of everything we do. Based in Kochi, we serve customers across Kerala and beyond.</p>
      <p data-reveal>Today we offer complete water solutions: pools and spas, fountains and water features, drinking and non-drinking water treatment, sewage and effluent treatment, UV purification and ultrafiltration. We also take on operation and maintenance contracts, and supply spares and consumables.</p>
      <p data-reveal>Our expertise covers the full range of equipment and chemicals. Under the Mira Pools name, we work with reputable and innovative companies who lead in their fields. Together with our construction experience, that lets us offer pool and spa services of a high standard at a sensible cost.</p>
    </div>
    <figure class="rounded-img" data-reveal="clip" style="aspect-ratio: 4 / 4.4">
      <?= picture(img('about_story'), 'Resort swimming pool built by Mira Pools', ['sizes' => '(min-width: 900px) 45vw, 100vw']) ?>
    </figure>
  </div>
</section>

<section class="section bg-sand">
  <div class="wrap">
    <div class="section-head section-head-split">
      <div>
        <p class="eyebrow" data-reveal><span class="num">02</span> Why people choose us</p>
        <h2 class="display h2" data-split>What sets us <em>apart.</em></h2>
      </div>
      <p class="section-head-note" data-reveal>The principles behind every pool, fountain and plant we take on.</p>
    </div>
    <div class="values">
      <?php
      $values = [
          ['Experienced technicians', 'A qualified team that takes time to understand your needs, and designs a pool or plant around them.'],
          ['Designs that fit', 'Pools and features that suit your landscape, your building and your budget.'],
          ['Turnkey projects', 'We handle everything from the first plan to final handover, for pools and treatment plants alike.'],
          ['Quality products', 'Equipment from internationally reputed brands, with spares available when you need them.'],
          ['Completed on schedule', 'We plan carefully so each project is commissioned and handed over within the agreed time.'],
          ['After-sales support', 'Handover is not the end. We stay with you for anything you need after commissioning.'],
          ['Our own laboratory', 'Our technicians in Kochi test water for physical, chemical and bacteriological quality.'],
          ['Design consultancy', 'In-house designers and structural consultancy to plan your pool to your budget.'],
      ];
      foreach ($values as $k => [$t, $d]): ?>
        <div class="value" data-reveal style="--d:<?= ($k % 4) * 0.08 ?>s">
          <span class="num"><?= str_pad((string) ($k + 1), 2, '0', STR_PAD_LEFT) ?></span>
          <h3><?= e($t) ?></h3>
          <p><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap two-col">
    <figure class="rounded-img" data-reveal="clip" style="aspect-ratio: 4 / 5">
      <?= picture(img('about_team'), 'Overflow swimming pool by Mira Pools', ['sizes' => '(min-width: 900px) 45vw, 100vw']) ?>
    </figure>
    <div>
      <p class="eyebrow" data-reveal><span class="num">03</span> Our expertise</p>
      <h2 class="display h2" data-split>We know pools <em>inside out.</em></h2>
      <p class="prose" data-reveal style="color:var(--muted)">Our strength is a thorough knowledge of every part of a pool, and the ability to understand what a particular client needs.</p>
      <ul class="check-list" data-reveal style="margin-top:26px">
        <?php foreach (['Pool construction', 'Filtration & accessories', 'Underwater illumination', 'Heating & ventilation systems', 'Water and waste water treatment', 'Equipment, spares & chemicals'] as $x): ?>
          <li><?= icon('check') ?> <span><?= e($x) ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<section class="section bg-sand">
  <div class="wrap two-col" style="align-items:start">
    <div>
      <p class="eyebrow" data-reveal><span class="num">04</span> A word from us</p>
      <h2 class="display h2" data-split>Prompt service, <em>always.</em></h2>
      <p data-reveal style="color:var(--muted)">We are happy to be considered for your next project, whether you are a homeowner, an architect, a builder or a pool contractor looking for a reliable partner.</p>
      <a class="btn btn-teal" href="/contact" data-reveal>Talk to our team <?= arrow() ?></a>
    </div>
    <div class="letter" data-reveal>
      <blockquote>We always strive to provide workable, cost-effective solutions for every customer, with one aim: water systems that run well, year after year.</blockquote>
      <cite><?= e($s['ceo']) ?><small>CEO, <?= e($s['company']) ?></small></cite>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow" data-reveal><span class="num">05</span> Our offices</p>
      <h2 class="display h2" data-split>Close to you, <em>wherever</em> you are in Kerala.</h2>
    </div>
    <div class="offices">
      <?php foreach ($s['branches'] as $k => $b): $hq = stripos($b['city'], 'head') !== false; ?>
        <a class="office<?= $hq ? ' office-hq' : '' ?>" href="<?= e(tel_link($b['phone'])) ?>" data-reveal style="--d:<?= $k * 0.08 ?>s">
          <?= icon('pin') ?>
          <strong><?= e(preg_replace('/\s*\(.*\)/', '', $b['city'])) ?></strong>
          <small><?= $hq ? 'Head office · ' : '' ?><?= e($b['district']) ?></small>
          <span><?= e($b['phone']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php cta_band(); ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
