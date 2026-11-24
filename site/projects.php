<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';

[$ongoing, $done] = projects_split();
$crumbs = [['Projects', '/projects']];

$items = [];
foreach (array_merge($ongoing, $done) as $i => $p) {
    $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => trim($p['name'] . ($p['location'] ? ', ' . $p['location'] : ''))];
}
$schema = [breadcrumb_schema($crumbs)];
if ($items) {
    $schema[] = ['@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => 'Mira Pools projects', 'itemListElement' => $items];
}

$page = [
    'title'       => 'Projects – Ongoing and Completed Swimming Pool Work in Kerala',
    'description' => 'Ongoing and completed swimming pool, spa, fountain and water treatment projects by Mira Pools across Kerala.',
    'path'        => '/projects',
    'image'       => img('projects_hero'),
    'schema'      => $schema,
    'body'        => 'has-dark-hero',
];
require __DIR__ . '/includes/header.php';

page_hero('Projects, <em>start to finish.</em>', 'Our projects', 'What we are building right now, and the work we have handed over across Kerala.', 'projects_hero', $crumbs);

// completed projects grouped by year, newest year first
$byYear = [];
foreach ($done as $p) {
    $byYear[(int) ($p['year'] ?? 0)][] = $p;
}
?>

<?php if (!$ongoing && !$done): ?>
<section class="section">
  <div class="wrap">
    <p class="empty-note">Our project list is being updated. In the meantime, have a look at the <a href="/gallery">gallery</a>.</p>
  </div>
</section>
<?php else: ?>

<section class="section proj-intro">
  <div class="wrap">
    <div class="proj-stats" data-reveal>
      <div><b><?= count($ongoing) ?></b><span>Ongoing <?= count($ongoing) === 1 ? 'project' : 'projects' ?></span></div>
      <div><b><?= count($done) ?></b><span>Completed <?= count($done) === 1 ? 'project' : 'projects' ?> listed</span></div>
      <a class="link-arrow" href="/gallery">See photos in the gallery <?= arrow() ?></a>
    </div>
  </div>
</section>

<?php if ($ongoing): ?>
<section class="section proj-ongoing">
  <div class="wrap">
    <div class="section-head section-head-split">
      <div>
        <p class="eyebrow" data-reveal><span class="num">01</span> Ongoing</p>
        <h2 class="display h2" data-split>Being built <em>right now.</em></h2>
      </div>
    </div>
    <div class="proj-cards">
      <?php foreach ($ongoing as $k => $p): ?>
        <article class="proj-card" data-reveal style="--d:<?= ($k % 3) * 0.08 ?>s">
          <span class="proj-live"><i></i> In progress</span>
          <h3><?= e($p['name']) ?></h3>
          <?php if (!empty($p['location'])): ?><p class="proj-loc"><?= icon('pin') ?> <?= e($p['location']) ?></p><?php endif; ?>
          <div class="proj-ripple" aria-hidden="true"></div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($done): ?>
<section class="section proj-done bg-sand">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow" data-reveal><span class="num"><?= $ongoing ? '02' : '01' ?></span> Completed</p>
      <h2 class="display h2" data-split>Handed over and <em>swimming.</em></h2>
    </div>
    <?php foreach ($byYear as $year => $list): ?>
      <div class="proj-year" data-reveal>
        <h3 class="proj-year-label"><?= $year ? (int) $year : 'Earlier' ?></h3>
        <ul class="proj-list">
          <?php foreach ($list as $p): ?>
            <li>
              <span class="proj-name"><?= e($p['name']) ?></span>
              <span class="proj-where"><?php if (!empty($p['location'])): ?><?= icon('pin') ?> <?= e($p['location']) ?><?php endif; ?></span>
              <span class="proj-when"><?= e(project_date($p)) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php endif; ?>

<?php cta_band('Your project could be next.', 'Tell us about your site and what you have in mind. We will visit and give you a clear plan.'); ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
