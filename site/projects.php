<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';

[$ongoing, $done] = projects_split();
$show = (string) ($_GET['show'] ?? '');
if (!in_array($show, ['ongoing', 'completed'], true)) {
    $show = '';
}

$groups = [
    'ongoing'   => ['title' => 'Ongoing projects', 'lead' => 'What our teams are building right now.', 'items' => $ongoing, 'slot' => 'projects_ongoing'],
    'completed' => ['title' => 'Completed projects', 'lead' => 'Pools, fountains and plants we have handed over across Kerala.', 'items' => $done, 'slot' => 'projects_completed'],
];

/** Cover for a group tile: the newest project photo, else the admin-set slot image. */
function group_cover(array $g): string
{
    foreach ($g['items'] as $p) {
        if (!empty($p['image'])) {
            return (string) $p['image'];
        }
    }
    return img($g['slot']);
}

$crumbs = [['Projects', '/projects']];
if ($show) {
    $crumbs[] = [$groups[$show]['title'], '/projects?show=' . $show];
}
$list = $show ? $groups[$show]['items'] : array_merge($ongoing, $done);
$schema = [breadcrumb_schema($crumbs)];
if ($list) {
    $schema[] = ['@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => $show ? $groups[$show]['title'] : 'Mira Pools projects',
        'itemListElement' => array_map(fn($p, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => trim($p['name'] . (!empty($p['location']) ? ', ' . $p['location'] : ''))], $list, array_keys($list))];
}

$page = [
    'title'       => $show ? $groups[$show]['title'] . ' in Kerala' :'Projects – Ongoing and Completed Swimming Pool Work in Kerala',
    'description' => $show
        ? $groups[$show]['lead'] . ' Swimming pools, spas, fountains and water treatment by Mira Pools.'
        : 'Ongoing and completed swimming pool, spa, fountain and water treatment projects by Mira Pools across Kerala.',
    'path'        => '/projects' . ($show ? '?show=' . $show : ''),
    'image'       => $show ? group_cover($groups[$show]) : img('projects_hero'),
    'schema'      => $schema,
    'body'        => 'has-dark-hero',
];
require __DIR__ . '/includes/header.php';

/** One project card with photo (or a water placeholder when there is none). */
function project_card(array $p, int $k): void
{
    $photo = (string) ($p['thumb'] ?? '') ?: (string) ($p['image'] ?? '');
    $done = ($p['status'] ?? '') === 'completed'; ?>
    <article class="pc" data-reveal style="--d:<?= ($k % 3) * 0.08 ?>s">
      <figure class="pc-img<?= $photo ? '' : ' pc-img-empty' ?>">
        <?php if ($photo): ?>
          <?= picture($photo, $p['name'] . (!empty($p['location']) ? ', ' . $p['location'] : '') . ' – Mira Pools', ['sizes' => '(min-width: 900px) 33vw, 100vw']) ?>
        <?php else: ?>
          <span aria-hidden="true"><?= icon('drop') ?></span>
        <?php endif; ?>
        <?php if ($done): ?>
          <span class="pc-tag"><?= e(project_date($p)) ?></span>
        <?php else: ?>
          <span class="pc-tag pc-tag-live"><i></i> In progress</span>
        <?php endif; ?>
      </figure>
      <div class="pc-body">
        <h3><?= e($p['name']) ?></h3>
        <?php if (!empty($p['location'])): ?><p class="pc-loc"><?= icon('pin') ?> <?= e($p['location']) ?></p><?php endif; ?>
      </div>
    </article>
<?php }

if ($show):
    $g = $groups[$show];
    $hero = '<em>' . e(ucfirst(strtok($g['title'], ' '))) . '</em> ' . e(substr($g['title'], strpos($g['title'], ' ') + 1)) . '.';
    page_hero($hero, 'Our projects', $g['lead'], 'projects_hero', $crumbs);
?>
<section class="section pg-section">
  <div class="wrap">
    <div class="pg-bar" data-reveal>
      <a class="pg-back" href="/projects"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12H5m6-6-6 6 6 6"/></svg> Back to projects</a>
      <span class="pg-count"><?= count($g['items']) ?> <?= count($g['items']) === 1 ? 'project' : 'projects' ?></span>
      <?php $other = $show === 'ongoing' ? 'completed' : 'ongoing'; ?>
      <a class="link-arrow" href="/projects?show=<?= $other ?>"><?= e($groups[$other]['title']) ?> <?= arrow() ?></a>
    </div>

    <?php if (!$g['items']): ?>
      <p class="empty-note">No <?= $show ?> projects to show yet. Please check back soon.</p>
    <?php elseif ($show === 'ongoing'): ?>
      <div class="pc-grid"><?php foreach ($g['items'] as $k => $p) { project_card($p, $k); } ?></div>
    <?php else:
        $byYear = [];
        foreach ($g['items'] as $p) {
            $byYear[(int) ($p['year'] ?? 0)][] = $p;
        }
        foreach ($byYear as $year => $items): ?>
        <div class="pc-year">
          <h2 class="pc-year-label" data-reveal><?= $year ? (int) $year : 'Earlier' ?></h2>
          <div class="pc-grid"><?php foreach ($items as $k => $p) { project_card($p, $k); } ?></div>
        </div>
    <?php endforeach; endif; ?>

    <div class="pg-foot" data-reveal>
      <a class="btn btn-ghost" href="/projects"><svg class="arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12H5m6-6-6 6 6 6"/></svg> Back to projects</a>
    </div>
  </div>
</section>

<?php else:
    page_hero('Projects, <em>start to finish.</em>', 'Our projects', 'What we are building right now, and the work we have handed over across Kerala.', 'projects_hero', $crumbs);
?>
<section class="section pt-section">
  <div class="wrap">
    <div class="pt-grid">
      <?php $n = 0; foreach ($groups as $key => $g): $n++; ?>
        <a class="pt" href="/projects?show=<?= $key ?>" data-reveal style="--d:<?= ($n - 1) * 0.12 ?>s">
          <div class="pt-media"><?= picture(group_cover($g), '', ['sizes' => '(min-width: 900px) 50vw, 100vw']) ?></div>
          <div class="pt-shade" aria-hidden="true"></div>
          <div class="caustics" aria-hidden="true"></div>
          <div class="pt-body">
            <span class="pt-num"><?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
            <?php if ($key === 'ongoing'): ?><span class="pc-tag pc-tag-live pt-live"><i></i> In progress</span><?php endif; ?>
            <h2><?= e($g['title']) ?></h2>
            <p><?= e($g['lead']) ?></p>
            <span class="pt-foot"><b><?= count($g['items']) ?></b> <?= count($g['items']) === 1 ? 'project' : 'projects' ?> <span class="pt-go"><?= arrow() ?></span></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php cta_band('Your project could be next.', 'Tell us about your site and what you have in mind. We will visit and give you a clear plan.'); ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
