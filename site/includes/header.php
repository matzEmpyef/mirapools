<?php
/**
 * Page head + site header. Expects $page:
 *   title, description, path, image (optional), preload (optional), schema (optional array of JSON-LD), body (optional class)
 */

declare(strict_types=1);

$page = ($page ?? []) + ['title' => '', 'description' => '', 'path' => '/', 'image' => '', 'preload' => '', 'schema' => [], 'body' => ''];
$brand = settings('brand');
$fullTitle = $page['title'] ? $page['title'] . ' | ' . $brand : $brand . ' – ' . settings('tagline');
$canonical = abs_url($page['path']);
$ogImage = $page['image'] ?: img('home_hero');
$ogImage = abs_url($ogImage);
$current = '/' . trim(strtok($page['path'], '?'), '/');
$current = $current === '/' ? '/' : $current;
$isActive = fn(string $href) => $href === '/' ? $current === '/' : str_starts_with($current, $href);
?><!doctype html>
<html lang="en-IN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e($page['description']) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="robots" content="<?= SITE_NOINDEX ? 'noindex, nofollow' : 'index, follow, max-image-preview:large' ?>">
<meta name="theme-color" content="#073b40">
<meta name="geo.region" content="IN-KL">
<meta name="geo.placename" content="Kochi, Kerala">

<meta property="og:type" content="website">
<meta property="og:locale" content="en_IN">
<meta property="og:site_name" content="<?= e($brand) ?>">
<meta property="og:title" content="<?= e($fullTitle) ?>">
<meta property="og:description" content="<?= e($page['description']) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($fullTitle) ?>">
<meta name="twitter:description" content="<?= e($page['description']) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">

<link rel="icon" href="/favicon.ico" sizes="48x48">
<link rel="icon" href="/assets/img/favicon-32.png" type="image/png" sizes="32x32">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://images.unsplash.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..650;1,9..144,300..500&family=Manrope:wght@400;500;600;700&display=swap">
<?php if ($page['preload']): ?>
<link rel="preload" as="image" href="<?= e($page['preload']) ?>" fetchpriority="high">
<?php endif; ?>
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<?php foreach (array_merge([site_schema()], $page['schema']) as $ld): ?>
<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?php endforeach; ?>
</head>
<body class="<?= e($page['body']) ?>">
<a class="skip" href="#main">Skip to content</a>

<header class="site-header" data-header>
  <div class="wrap header-inner">
    <a class="brand" href="/" aria-label="<?= e($brand) ?> – home">
      <?= logo_svg() ?>
    </a>

    <nav class="nav" aria-label="Main">
      <ul>
        <?php foreach (nav_pages() as $href => $label): if ($href === '/') continue; ?>
          <li><a href="<?= $href ?>"<?= $isActive($href) ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <div class="header-actions">
      <a class="header-phone" href="<?= e(tel_link(settings('phone'))) ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1l-2.3 2.2Z"/></svg>
        <span><?= e(settings('phone')) ?></span>
      </a>
      <a class="btn btn-sm btn-sun" href="/contact">Get a quote</a>
      <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
        <span class="sr-only">Menu</span><i></i><i></i>
      </button>
    </div>
  </div>
</header>

<div class="mobile-menu" id="mobile-menu" data-mobile-menu hidden>
  <div class="mobile-menu-inner">
    <ol>
      <?php $n = 0; foreach (nav_pages() as $href => $label): $n++; ?>
        <li style="--i:<?= $n ?>"><a href="<?= $href ?>"<?= $isActive($href) ? ' aria-current="page"' : '' ?>><small><?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></small><?= e($label) ?></a></li>
      <?php endforeach; ?>
    </ol>
    <div class="mobile-menu-foot">
      <a href="<?= e(tel_link(settings('phone'))) ?>"><?= e(settings('phone')) ?></a>
      <a href="mailto:<?= e(settings('email')) ?>"><?= e(settings('email')) ?></a>
    </div>
  </div>
</div>

<div class="depth-gauge" aria-hidden="true" data-depth>
  <span class="depth-label">Depth</span>
  <span class="depth-track"><i data-depth-fill></i></span>
  <span class="depth-value" data-depth-value>0.0 m</span>
</div>

<main id="main">
