<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';

$s = all_settings();
$page = [
    'title'       => 'Swimming Pool Construction in Kerala',
    'description' => 'Mira Pools designs, builds and maintains swimming pools, spas, jacuzzis, fountains and water treatment plants for homes, resorts and hotels across Kerala. Offices in Kochi, Kozhikode, Kasaragod and Thiruvananthapuram.',
    'path'        => '/',
    'preload'     => preg_replace('/w=\d+/', 'w=1600', img('home_hero')),
    'body'        => 'has-dark-hero',
];
require __DIR__ . '/includes/header.php';

$featured = gallery_featured(8);
$cats = gallery_categories();
?>

<section class="hero">
  <div class="hero-media" data-parallax="0.22">
    <?= picture(img('home_hero'), 'Swimming pool at a tropical villa at dusk', ['eager' => true]) ?>
  </div>
  <div class="caustics" aria-hidden="true"></div>
  <div class="hero-shade" aria-hidden="true"></div>

  <div class="wrap hero-inner">
    <p class="eyebrow eyebrow-light" data-reveal>Swimming pools · Spas · Fountains · Water treatment</p>
    <h1 class="display hero-title" data-split>Swimming pools, crafted for <em>Kerala</em> living.</h1>
    <p class="hero-lead" data-reveal style="--d:.35s">We design, build and look after swimming pools, spas, fountains and water treatment systems for homes, resorts and hotels.</p>
    <div class="hero-actions" data-reveal style="--d:.5s">
      <a class="btn btn-sun" href="/contact">Plan your pool <?= arrow() ?></a>
      <a class="btn btn-ghost-light" href="/gallery">See our work</a>
    </div>
  </div>

  <div class="hero-foot wrap" data-reveal style="--d:.7s">
    <span class="hero-foot-label">Offices across Kerala</span>
    <ul class="hero-offices">
      <li>Kasaragod</li><li>Kozhikode</li><li>Kochi</li><li>Thiruvananthapuram</li>
    </ul>
    <a class="scroll-cue" href="#intro"><span>Dive in</span><i></i></a>
  </div>
</section>

<div class="marquee" aria-hidden="true">
  <div class="marquee-track">
    <?php for ($r = 0; $r < 2; $r++): ?>
      <span>Swimming pools</span><b>✦</b><span>Spas &amp; jacuzzis</span><b>✦</b><span>Fountains</span><b>✦</b><span>Water treatment</span><b>✦</b><span>Sewage treatment</span><b>✦</b><span>Saunas &amp; steam</span><b>✦</b><span>Maintenance</span><b>✦</b>
    <?php endfor; ?>
  </div>
</div>

<section class="section intro" id="intro">
  <div class="wrap intro-grid">
    <div class="intro-copy">
      <p class="eyebrow" data-reveal><span class="num">01</span> Who we are</p>
      <h2 class="display h2" data-split>One team for everything that <em>holds water.</em></h2>
      <p class="lead" data-reveal>Mira Water Management offers complete water solutions, from swimming pools and spas to fountains, drinking water plants and sewage treatment.</p>
      <p data-reveal>Our pool professionals and design engineers know pool construction, filtration, underwater lighting, heating and ventilation inside out. We work with reputable equipment makers, and we look for the solution that works best for your site and your budget, not the most expensive one.</p>
      <a class="link-arrow" href="/about" data-reveal>More about Mira <?= arrow() ?></a>
    </div>
    <div class="intro-media">
      <figure class="intro-img-a" data-reveal="clip"><?= picture(img('home_intro'), 'Villa swimming pool built by Mira Pools', ['sizes' => '(min-width: 900px) 45vw, 100vw']) ?></figure>
      <figure class="intro-img-b" data-reveal="clip" style="--d:.2s"><?= picture(img_thumb('home_intro_2'), 'Mosaic-tiled jacuzzi', ['sizes' => '(min-width: 900px) 22vw, 50vw']) ?></figure>
      <div class="badge-spin" aria-hidden="true">
        <svg viewBox="0 0 120 120"><defs><path id="circ" d="M60 60m-46 0a46 46 0 1 1 92 0a46 46 0 1 1-92 0"/></defs><text><textPath href="#circ" textLength="286" lengthAdjust="spacing">MIRA POOLS · WATER MANAGEMENT · KERALA · </textPath></text></svg>
        <span><?= icon('drop') ?></span>
      </div>
    </div>
  </div>
</section>

<section class="section services-dark">
  <div class="tiles-bg" aria-hidden="true"></div>
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow eyebrow-light" data-reveal><span class="num">02</span> What we do</p>
      <h2 class="display h2" data-split>Ten services, <em>one</em> phone number.</h2>
      <p class="section-head-note" data-reveal>Whether you need a new pool, a fountain that works again, or clean water for a whole building, it starts with a site visit.</p>
    </div>

    <ol class="svc-list" data-svc-list>
      <?php $i = 0; foreach (services() as $slug => $svc): $i++; ?>
        <li data-reveal style="--d:<?= min($i, 6) * 0.04 ?>s">
          <a href="/services/<?= e($slug) ?>" data-img="<?= e(img_thumb('svc_' . $slug)) ?>">
            <span class="svc-num"><?= str_pad((string) $i, 2, '0', STR_PAD_LEFT) ?></span>
            <span class="svc-name"><?= e($svc['name']) ?></span>
            <span class="svc-short"><?= e($svc['short']) ?></span>
            <span class="svc-go"><?= arrow() ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ol>
    <div class="svc-float" data-svc-float aria-hidden="true"><img alt="" src="data:image/gif;base64,R0lGODlhAQABAAAAACw="></div>
  </div>
</section>

<?php if ($featured): ?>
<section class="section work">
  <div class="wrap work-head">
    <div>
      <p class="eyebrow" data-reveal><span class="num">03</span> Recent work</p>
      <h2 class="display h2" data-split>Pools we're <em>proud</em> of.</h2>
    </div>
    <div class="work-controls" data-reveal>
      <button type="button" class="round-btn" data-track-prev aria-label="Previous projects"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12H5m6-6-6 6 6 6"/></svg></button>
      <button type="button" class="round-btn" data-track-next aria-label="Next projects"><?= arrow() ?></button>
    </div>
  </div>
  <div class="work-track" data-track tabindex="0" aria-label="Featured projects">
    <?php foreach ($featured as $n => $item): ?>
      <a class="work-card" href="/gallery#<?= e($item['id']) ?>" data-reveal style="--d:<?= $n * 0.06 ?>s">
        <figure>
          <?= picture($item['thumb'] ?: $item['src'], $item['alt'] ?: $item['title'], ['sizes' => '(min-width: 900px) 34vw, 80vw']) ?>
        </figure>
        <span class="work-meta"><span class="work-cat"><?= e($cats[$item['category']] ?? '') ?></span><span class="work-title"><?= e($item['title']) ?></span><?php if (!empty($item['location'])): ?><span class="work-loc"><?= e($item['location']) ?></span><?php endif; ?></span>
      </a>
    <?php endforeach; ?>
    <a class="work-card work-card-more" href="/gallery">
      <span>View the full gallery <?= arrow() ?></span>
    </a>
  </div>
</section>
<?php endif; ?>

<section class="promise">
  <div class="promise-media" data-parallax="0.15"><?= picture(img('home_quote'), 'Overflow-edge pool at a private villa') ?></div>
  <div class="promise-shade" aria-hidden="true"></div>
  <div class="wrap promise-inner">
    <p class="eyebrow eyebrow-light" data-reveal>Our promise</p>
    <blockquote data-split>
      “The best possible swimming pool at the best possible price, supplied and professionally installed with the <em>least amount of stress</em> and interruption.”
    </blockquote>
  </div>
</section>

<section class="section process">
  <div class="wrap">
    <div class="section-head section-head-split">
      <div>
        <p class="eyebrow" data-reveal><span class="num">04</span> How we work</p>
        <h2 class="display h2" data-split>From first visit to <em>first swim.</em></h2>
      </div>
      <p class="section-head-note" data-reveal>You deal with one team from start to finish, and long after handover.</p>
    </div>

    <div class="process-wrap">
      <svg class="process-line" viewBox="0 0 1200 120" preserveAspectRatio="none" aria-hidden="true"><path data-draw d="M0 60 C 100 10, 200 110, 300 60 S 500 10, 600 60 S 800 110, 900 60 S 1100 10, 1200 60"/></svg>
      <ol class="process-steps">
        <?php
        $steps = [
            ['Site visit', 'We see the site, understand how you will use the pool or system, and check access, soil and services.'],
            ['Design & proposal', 'You get a layout, the equipment we recommend and a clear quote, adjusted until it is right for you.'],
            ['Build & install', 'Our team builds the structure and installs filtration, lighting and fittings, keeping disruption low.'],
            ['Handover', 'We test and commission everything, then show you how to run it day to day.'],
            ['Care', 'Maintenance contracts, spares and chemicals keep the water clear for years to come.'],
        ];
        foreach ($steps as $k => [$t, $d]): ?>
          <li data-reveal style="--d:<?= $k * 0.08 ?>s">
            <span class="step-dot"><?= str_pad((string) ($k + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <h3><?= e($t) ?></h3>
            <p><?= e($d) ?></p>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>

<section class="section expertise">
  <div class="wrap expertise-grid">
    <div class="expertise-copy">
      <p class="eyebrow" data-reveal><span class="num">05</span> Inside every pool</p>
      <h2 class="display h2" data-split>The parts you don't see <em>matter most.</em></h2>
      <p data-reveal>A pool is only as good as the systems behind it. We handle each one in-house, and we supply the equipment and spares to keep them running.</p>
      <ul class="chip-list" data-reveal>
        <li>Plant room equipment</li><li>Basin fittings</li><li>Underwater lights</li><li>Ladders &amp; handrails</li><li>Jacuzzi equipment</li><li>Saunas</li><li>Steam generators</li><li>Fish pond filters</li><li>Cleaning tools</li><li>Chemicals</li><li>Water testing lab</li>
      </ul>
    </div>
    <div class="expertise-cards">
      <?php
      $cards = [
          ['build', 'Pool construction', 'Structure, waterproofing, tiling and mosaic, built to last through the monsoon.'],
          ['filter', 'Filtration & accessories', 'Pumps, filters and dosing sized to your pool, for clear water at a sensible running cost.'],
          ['light', 'Underwater lighting', 'Lights that make the pool as beautiful at night as it is in the day.'],
          ['heat', 'Heating & ventilation', 'Comfortable water and fresh air for indoor pools and spas.'],
      ];
      foreach ($cards as $k => [$ic, $t, $d]): ?>
        <article class="x-card" data-reveal style="--d:<?= $k * 0.08 ?>s">
          <span class="x-icon"><?= icon($ic) ?></span>
          <h3><?= e($t) ?></h3>
          <p><?= e($d) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section coverage">
  <div class="wrap coverage-grid">
    <div class="coverage-copy">
      <p class="eyebrow" data-reveal><span class="num">06</span> Where we work</p>
      <h2 class="display h2" data-split>From Kasaragod to <em>Thiruvananthapuram.</em></h2>
      <p data-reveal>With offices along the length of Kerala, our teams reach your site quickly, for building work and for service calls. Call the office nearest to you.</p>
      <a class="btn btn-teal" href="/contact" data-reveal>Find your nearest office <?= arrow() ?></a>
    </div>
    <div class="coast" data-coast>
      <svg viewBox="0 0 260 640" aria-hidden="true">
        <defs>
          <linearGradient id="sea" x1="1" y1="0" x2="0" y2="0">
            <stop offset="0" stop-color="#7fd3dc" stop-opacity=".45"/>
            <stop offset="1" stop-color="#7fd3dc" stop-opacity="0"/>
          </linearGradient>
        </defs>
        <path class="coast-sea" d="M0 0h70c8 60-14 120 4 190s34 120 52 210 46 160 70 240H0Z" fill="url(#sea)"/>
        <g class="coast-ripples">
          <path d="M14 120c10-6 20-6 30 0"/><path d="M22 300c10-6 20-6 30 0"/><path d="M60 470c10-6 20-6 30 0"/><path d="M30 560c10-6 20-6 30 0"/>
        </g>
        <text class="coast-label" x="30" y="380" transform="rotate(-72 30 380)">Arabian Sea</text>
        <path class="coast-line" data-coast-path data-draw d="M70 0c8 60-14 120 4 190s34 120 52 210 46 160 70 240"/>
      </svg>
      <?php
      $pins = [
          ['Kasaragod', 0.04, 'Kasaragod'],
          ['Kozhikode', 0.33, 'Calicut'],
          ['Kochi', 0.66, 'Head office'],
          ['Thiruvananthapuram', 0.96, 'Trivandrum'],
      ];
      $phones = [];
      foreach ($s['branches'] as $b) {
          $phones[strtolower(strtok($b['city'], ' '))] = $b['phone'];
      }
      foreach ($pins as $k => [$city, $at, $note]):
          $phone = $phones[strtolower($city)] ?? '';
      ?>
        <div class="pin<?= $note === 'Head office' ? ' pin-hq' : '' ?><?= $at > 0.9 ? ' pin-left' : '' ?>" data-pin="<?= $at ?>" data-reveal style="--d:<?= 0.2 + $k * 0.15 ?>s">
          <i></i>
          <div class="pin-card">
            <strong><?= e($city) ?></strong>
            <small><?= e($note) ?></small>
            <?php if ($phone): ?><a href="<?= e(tel_link($phone)) ?>"><?= e($phone) ?></a><?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php cta_band(); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
