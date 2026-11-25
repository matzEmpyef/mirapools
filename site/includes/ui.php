<?php
/**
 * Small view helpers shared by the public pages.
 */

declare(strict_types=1);

/**
 * Full company logo. "light" has a white tagline for dark backgrounds, "dark" a dark one;
 * "both" prints the two and CSS shows the right one (the header changes colour on scroll).
 */
function logo_svg(string $variant = 'both'): string
{
    $img = fn(string $v, string $alt) => '<img class="logo-mark logo-' . $v . '" src="' . asset('img/logo-full-' . $v . '.png') . '" width="435" height="300" alt="' . $alt . '">';
    if ($variant !== 'both') {
        return $img($variant, 'Mira Pools – Water Management');
    }
    return $img('light', 'Mira Pools – Water Management') . $img('dark', '');
}

/** Responsive <img>. Unsplash URLs get a srcset; local files are used as-is. */
function picture(string $src, string $alt, array $o = []): string
{
    $o += ['class' => '', 'eager' => false, 'sizes' => '100vw', 'w' => 0, 'h' => 0];
    $attrs = ' src="' . e($src) . '" alt="' . e($alt) . '"';
    if (str_contains($src, 'images.unsplash.com')) {
        $set = [];
        foreach ([640, 1024, 1600, 2200] as $w) {
            $set[] = e(preg_replace('/w=\d+/', 'w=' . $w, $src)) . ' ' . $w . 'w';
        }
        $attrs .= ' srcset="' . implode(', ', $set) . '" sizes="' . e($o['sizes']) . '"';
    }
    if ($o['w'] && $o['h']) {
        $attrs .= ' width="' . (int) $o['w'] . '" height="' . (int) $o['h'] . '"';
    }
    $attrs .= $o['eager'] ? ' fetchpriority="high"' : ' loading="lazy"';
    $attrs .= ' decoding="async"';
    if ($o['class']) {
        $attrs .= ' class="' . e($o['class']) . '"';
    }
    return '<img' . $attrs . '>';
}

function arrow(): string
{
    return '<svg class="arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h15m-6-6 6 6-6 6"/></svg>';
}

/** Line icons for services and features. */
function icon(string $name): string
{
    $p = [
        'pool'      => '<path d="M3 17c2 1.3 4 1.3 6 0s4-1.3 6 0 4 1.3 6 0M3 21c2 1.3 4 1.3 6 0s4-1.3 6 0 4 1.3 6 0M8 14V5a2 2 0 0 1 4 0M16 14V5a2 2 0 0 0-4 0M8 8h8M8 11h8"/>',
        'filter'    => '<path d="M8 3h8v3H8zM7 6h10l-1 13a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2L7 6ZM10 11h4M10 15h4"/>',
        'spa'       => '<path d="M4 14h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4v-3ZM8 10c0-2 2-2 2-4M12 10c0-2 2-2 2-4M16 10c0-2 2-2 2-4"/>',
        'fountain'  => '<path d="M12 13V4M12 4c-3 0-5 3-5 6M12 4c3 0 5 3 5 6M4 17h16l-2 4H6l-2-4ZM8 13h8v4H8z"/>',
        'drop'      => '<path d="M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11ZM9.5 14.5a2.5 2.5 0 0 0 2.5 2.5"/>',
        'plant'     => '<path d="M3 21V10l5 3V10l5 3V10l5 3v8H3ZM18 13V4h3v17M7 17h2M12 17h2"/>',
        'uv'        => '<path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8Z"/>',
        'fish'      => '<path d="M3 12c3-4 7-5 11-4 2 .5 4 2 5 4-1 2-3 3.5-5 4-4 1-8 0-11-4ZM19 12l2-3v6l-2-3ZM14 11h.01"/>',
        'wrench'    => '<path d="M14.7 6.3a4 4 0 0 1 5 5l-2.2-.6-1.6 1.6.6 2.2a4 4 0 0 1-5-5L4 17v3h3l7.5-7.5"/>',
        'box'       => '<path d="M3 7l9-4 9 4-9 4-9-4ZM3 7v10l9 4 9-4V7M12 11v10"/>',
        'light'     => '<path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-3.5 10.9c.6.4 1 1.1 1 1.8V16h5v-.3c0-.7.4-1.4 1-1.8A6 6 0 0 0 12 3Z"/>',
        'heat'      => '<path d="M8 3c0 3-3 4-3 8a7 7 0 0 0 14 0c0-4-3-5-3-8M12 8c0 2-2 3-2 5a2 2 0 0 0 4 0c0-2-2-3-2-5Z"/>',
        'build'     => '<path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6"/>',
        'check'     => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'phone'     => '<path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1l-2.3 2.2Z"/>',
        'mail'      => '<path d="M3 6h18v12H3zM3 7l9 6 9-6"/>',
        'pin'       => '<path d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12ZM12 11.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>',
        'chat'      => '<path d="M4 20l1.3-3.9A8 8 0 1 1 8 19.4L4 20Z"/>',
        'clock'     => '<path d="M12 7v5l3 2M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/>',
    ];
    return '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true">' . ($p[$name] ?? '') . '</svg>';
}

function service_icon(string $slug): string
{
    $map = [
        'swimming-pool-construction' => 'pool',
        'pool-filtration-systems'    => 'filter',
        'spa-sauna-steam'            => 'spa',
        'fountains-water-features'   => 'fountain',
        'water-treatment-plants'     => 'drop',
        'sewage-effluent-treatment'  => 'plant',
        'uv-ultrafiltration'         => 'uv',
        'fish-pond-filtration'       => 'fish',
        'maintenance-amc'            => 'wrench',
        'pool-equipment-spares'      => 'box',
    ];
    return icon($map[$slug] ?? 'drop');
}

/** Gallery category that best matches a service, for "related work". */
function service_gallery_category(string $slug): string
{
    return match ($slug) {
        'spa-sauna-steam' => 'spas',
        'fountains-water-features' => 'fountains',
        'fish-pond-filtration' => 'ponds',
        'water-treatment-plants', 'sewage-effluent-treatment', 'uv-ultrafiltration' => 'treatment',
        'pool-equipment-spares', 'pool-filtration-systems' => 'equipment',
        default => 'pools',
    };
}

/** Inner-page hero with breadcrumb. $crumbs: [[label, href], ...] (last one is current). */
function page_hero(string $title, string $kicker, string $lead, string $slot, array $crumbs): void
{
    ?>
    <section class="page-hero">
      <div class="page-hero-media" data-parallax="0.18">
        <?= picture(img($slot), '', ['eager' => true]) ?>
        <div class="caustics" aria-hidden="true"></div>
      </div>
      <div class="wrap page-hero-inner">
        <nav class="crumbs" aria-label="Breadcrumb">
          <ol>
            <li><a href="/">Home</a></li>
            <?php foreach ($crumbs as $i => [$label, $href]): ?>
              <li><?php if ($i < count($crumbs) - 1): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?></li>
            <?php endforeach; ?>
          </ol>
        </nav>
        <p class="eyebrow eyebrow-light" data-reveal><?= e($kicker) ?></p>
        <h1 class="display" data-split><?= $title ?></h1>
        <?php if ($lead): ?><p class="page-hero-lead" data-reveal style="--d:.25s"><?= e($lead) ?></p><?php endif; ?>
      </div>
      <?= wave_edge('paper') ?>
    </section>
    <?php
}

/** Animated wave edge at the bottom of a dark section. */
function wave_edge(string $tone = 'paper'): string
{
    return '<div class="wave-edge wave-' . e($tone) . '" aria-hidden="true">'
        . '<svg viewBox="0 0 1440 90" preserveAspectRatio="none"><path class="w1" d="M0 50c120-26 240-26 360 0s240 26 360 0 240-26 360 0 240 26 360 0v40H0Z"/><path class="w2" d="M0 64c120 20 240 20 360 0s240-20 360 0 240 20 360 0 240-20 360 0v26H0Z"/></svg>'
        . '</div>';
}

function cta_band(string $title = 'Planning a pool, or fixing one?', string $text = 'Tell us about your site. We will visit, listen and give you an honest plan and price.'): void
{
    ?>
    <section class="cta-band">
      <div class="cta-media" data-parallax="0.12"><?= picture(img('home_cta'), '') ?></div>
      <div class="caustics caustics-strong" aria-hidden="true"></div>
      <div class="wrap cta-inner">
        <p class="eyebrow eyebrow-light" data-reveal>Let's talk water</p>
        <h2 class="display" data-split><?= e($title) ?></h2>
        <p data-reveal style="--d:.15s"><?= e($text) ?></p>
        <div class="cta-actions" data-reveal style="--d:.25s">
          <a class="btn btn-sun" href="/contact">Send an enquiry <?= arrow() ?></a>
          <a class="btn btn-ghost-light" href="<?= e(wa_link('Hello Mira Pools, I would like to know more about ')) ?>" target="_blank" rel="noopener"><?= icon('chat') ?> WhatsApp us</a>
          <a class="btn btn-ghost-light" href="<?= e(tel_link(settings('phone'))) ?>"><?= icon('phone') ?> <?= e(settings('phone')) ?></a>
        </div>
      </div>
    </section>
    <?php
}

/* ---------- structured data ---------- */

function site_schema(): array
{
    $s = all_settings();
    $sameAs = array_values(array_filter([$s['facebook'], $s['instagram'], $s['youtube']]));
    $departments = [];
    foreach ($s['branches'] as $b) {
        if (stripos($b['city'], 'head') !== false) {
            continue;
        }
        $departments[] = [
            '@type'     => 'HomeAndConstructionBusiness',
            'name'      => $s['brand'] . ' – ' . $b['city'],
            'telephone' => $b['phone'],
            'address'   => ['@type' => 'PostalAddress', 'addressLocality' => $b['city'], 'addressRegion' => 'Kerala', 'addressCountry' => 'IN'],
        ];
    }
    $ld = [
        '@context'    => 'https://schema.org',
        '@type'       => 'HomeAndConstructionBusiness',
        '@id'         => abs_url('/#business'),
        'name'        => $s['brand'],
        'legalName'   => $s['company'],
        'url'         => abs_url('/'),
        'logo'        => abs_url('/assets/img/logo-full.png'),
        'image'       => abs_url(img('home_hero')),
        'description' => 'Swimming pool construction, pool and spa filtration, fountains, water treatment and sewage treatment plants, and maintenance contracts across Kerala.',
        'telephone'   => $s['phone'],
        'email'       => $s['email'],
        'address'     => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $s['address_line'],
            'addressLocality' => $s['locality'] . ', ' . $s['city'],
            'addressRegion'   => $s['region'],
            'postalCode'      => $s['postcode'],
            'addressCountry'  => 'IN',
        ],
        'areaServed'  => ['@type' => 'State', 'name' => 'Kerala'],
        'employee'    => ['@type' => 'Person', 'name' => $s['ceo'], 'jobTitle' => 'CEO'],
        'department'  => $departments,
    ];
    if ($sameAs) {
        $ld['sameAs'] = $sameAs;
    }
    return $ld;
}

function breadcrumb_schema(array $crumbs): array
{
    $items = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => abs_url('/')]];
    foreach ($crumbs as $i => [$label, $href]) {
        $items[] = ['@type' => 'ListItem', 'position' => $i + 2, 'name' => $label, 'item' => abs_url($href)];
    }
    return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
}
