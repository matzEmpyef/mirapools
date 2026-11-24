<?php
defined('ADMIN_PAGE') || exit;

$gallery = gallery_all();
$featured = count(array_filter($gallery, fn($i) => !empty($i['featured'])));
$unread = unread_enquiries();
$totalEnq = count(enquiries_all());

admin_head('Dashboard', 'dashboard');
page_title('Dashboard', 'Welcome back. Here is how the website looks today.');
?>
<div class="stats">
  <a class="card stat" href="<?= e(admin_url('gallery')) ?>"><b><?= count($gallery) ?></b><span>Photos in the gallery</span></a>
  <a class="card stat" href="<?= e(admin_url('gallery')) ?>"><b><?= $featured ?></b><span>Featured on the home page</span></a>
  <a class="card stat <?= $unread ? 'stat-alert' : '' ?>" href="<?= e(admin_url('enquiries')) ?>"><b><?= $unread ?></b><span>New enquiries<?= $totalEnq ? ' (of ' . $totalEnq . ')' : '' ?></span></a>
</div>

<div class="grid-2">
  <section class="card">
    <h2>Quick links</h2>
    <div class="quick">
      <a class="btn btn-primary" href="<?= e(admin_url('gallery')) ?>">Add gallery photos</a>
      <a class="btn" href="<?= e(admin_url('projects')) ?>">Add or edit projects</a>
      <a class="btn" href="<?= e(admin_url('images')) ?>">Change site images</a>
      <a class="btn" href="<?= e(admin_url('enquiries')) ?>">Read enquiries</a>
      <a class="btn" href="<?= e(admin_url('settings')) ?>">Contact details</a>
      <a class="btn btn-ghost" href="/" target="_blank" rel="noopener">View the live website</a>
    </div>
  </section>
  <section class="card">
    <h2>How to</h2>
    <ul class="howto">
      <li><b>Add photos:</b> go to Gallery, choose several photos at once, pick a category and press Upload.</li>
      <li><b>Put photos on the home page:</b> tick "Show on home page" when uploading, or edit a photo later.</li>
      <li><b>Change the order:</b> drag photos on a computer, or use the arrow buttons on a phone.</li>
      <li><b>Change a page photo:</b> go to Site images and upload a new one. "Reset" brings the original back.</li>
      <li><b>Projects:</b> under Projects, press "Add project", enter the name and location, and mark it Ongoing or Completed (with the month and year).</li>
      <li><b>Phone, email, address:</b> edit them under Settings and they update across the whole site.</li>
    </ul>
  </section>
</div>
<?php admin_foot();
