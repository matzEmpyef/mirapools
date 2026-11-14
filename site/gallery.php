<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';

$crumbs = [['Gallery', '/gallery']];
$items = gallery_all();
$cats = gallery_categories();
$counts = array_count_values(array_column($items, 'category'));

$imagesLd = array_map(fn($g) => [
    '@type'      => 'ImageObject',
    'contentUrl' => abs_url($g['src']),
    'name'       => $g['title'],
    'caption'    => $g['alt'] ?: $g['title'],
], array_slice($items, 0, 30));

$page = [
    'title'       => 'Swimming Pool & Water Feature Gallery – Projects in Kerala',
    'description' => 'Photos of swimming pools, jacuzzis, fountains, water features and water treatment systems by Mira Pools for villas, resorts and hotels across Kerala.',
    'path'        => '/gallery',
    'image'       => $items[0]['src'] ?? img('gallery_hero'),
    'schema'      => [breadcrumb_schema($crumbs), ['@context' => 'https://schema.org', '@type' => 'ImageGallery', 'name' => 'Mira Pools project gallery', 'image' => $imagesLd]],
    'body'        => 'has-dark-hero',
];
require __DIR__ . '/includes/header.php';

page_hero('Our work, <em>in pictures.</em>', 'Gallery', 'Pools, spas, fountains and water systems we have built and installed for homes, resorts and hotels.', 'gallery_hero', $crumbs);
?>

<section class="section" style="padding-top:70px">
  <div class="wrap">
    <?php if (!$items): ?>
      <p class="empty-note">New photos are on their way. Please check back soon.</p>
    <?php else: ?>
      <div class="filters" role="group" aria-label="Filter photos">
        <button class="filter" type="button" data-filter="all" aria-pressed="true">All <sup><?= count($items) ?></sup></button>
        <?php foreach ($cats as $key => $label): if (empty($counts[$key])) continue; ?>
          <button class="filter" type="button" data-filter="<?= e($key) ?>" aria-pressed="false"><?= e($label) ?> <sup><?= (int) $counts[$key] ?></sup></button>
        <?php endforeach; ?>
      </div>

      <div class="masonry" data-gallery>
        <?php foreach ($items as $k => $g):
            $catLabel = $cats[$g['category']] ?? ''; ?>
          <button type="button" class="g-item" id="<?= e($g['id']) ?>" data-g-item
                  data-cat="<?= e($g['category']) ?>" data-cat-label="<?= e($catLabel) ?>"
                  data-full="<?= e($g['src']) ?>" data-title="<?= e($g['title']) ?>" data-alt="<?= e($g['alt'] ?: $g['title']) ?>" data-loc="<?= e($g['location'] ?? '') ?>"
                  aria-label="Open photo: <?= e($g['title']) ?>">
            <figure>
              <?= picture($g['thumb'] ?: $g['src'], $g['alt'] ?: $g['title'], ['w' => (int) ($g['width'] ?? 0), 'h' => (int) ($g['height'] ?? 0), 'eager' => $k < 3]) ?>
              <figcaption><small><?= e($catLabel) ?></small><strong><?= e($g['title']) ?></strong></figcaption>
            </figure>
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<div class="lightbox" data-lightbox aria-hidden="true" role="dialog" aria-modal="true" aria-label="Photo viewer">
  <img src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" alt="">
  <div class="lightbox-cap"><strong data-lb-title></strong><small data-lb-meta></small></div>
  <button class="lb-btn lb-close" type="button" data-lb-close aria-label="Close"><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
  <button class="lb-btn lb-prev" type="button" data-lb-prev aria-label="Previous photo"><svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg></button>
  <button class="lb-btn lb-next" type="button" data-lb-next aria-label="Next photo"><svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></button>
</div>

<?php cta_band('Like what you see?', 'Every project starts with a conversation. Tell us about yours.'); ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
