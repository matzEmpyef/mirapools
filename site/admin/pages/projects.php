<?php
defined('ADMIN_PAGE') || exit;

$thisYear = (int) date('Y');
$months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $list = projects_all();
    $find = function (string $id) use ($list): int {
        foreach ($list as $i => $p) {
            if (($p['id'] ?? '') === $id) {
                return $i;
            }
        }
        return -1;
    };

    if ($action === 'save') {
        $name = clean_text($_POST['name'] ?? '', 160);
        $location = clean_text($_POST['location'] ?? '', 120);
        $status = ($_POST['status'] ?? '') === 'completed' ? 'completed' : 'ongoing';
        $year = (int) ($_POST['year'] ?? 0);
        $month = (int) ($_POST['month'] ?? 0);
        if ($name === '') {
            flash('error', 'Please enter the project name.');
            redirect_to('projects');
        }
        if ($status === 'completed' && ($year < 1990 || $year > $thisYear + 1)) {
            flash('error', 'Please choose the year the project was completed.');
            redirect_to('projects');
        }
        $record = [
            'name'     => $name,
            'location' => $location,
            'status'   => $status,
            'year'     => $status === 'completed' ? $year : 0,
            'month'    => $status === 'completed' && $month >= 1 && $month <= 12 ? $month : 0,
        ];
        $id = (string) ($_POST['id'] ?? '');
        if ($id !== '') {
            $i = $find($id);
            if ($i < 0) {
                flash('error', 'That project no longer exists.');
                redirect_to('projects');
            }
            $list[$i] = $record + $list[$i];
            $msg = 'Project saved.';
        } else {
            array_unshift($list, ['id' => 'p' . bin2hex(random_bytes(5))] + $record + ['created' => now_iso()]);
            $msg = 'Project added.';
        }
        projects_save($list) ? flash('ok', $msg) : flash('error', 'Could not save. Check that the data folder is writable.');
        redirect_to('projects');
    }

    if ($action === 'delete') {
        $i = $find((string) ($_POST['id'] ?? ''));
        if ($i >= 0) {
            array_splice($list, $i, 1);
            projects_save($list) ? flash('ok', 'Project deleted.') : flash('error', 'Could not delete. Check that the data folder is writable.');
        }
        redirect_to('projects');
    }

    flash('error', 'Unknown action.');
    redirect_to('projects');
}

[$ongoing, $done] = projects_split();
$self = admin_url('projects');

admin_head('Projects', 'projects');
page_title('Projects', 'Ongoing and completed projects shown on the Projects page of the website.');

$row = function (array $p) use ($self): void {
    $id = (string) ($p['id'] ?? ''); ?>
    <tr data-id="<?= e($id) ?>" data-name="<?= e((string) $p['name']) ?>" data-location="<?= e((string) ($p['location'] ?? '')) ?>"
        data-status="<?= e((string) ($p['status'] ?? 'ongoing')) ?>" data-year="<?= (int) ($p['year'] ?? 0) ?>" data-month="<?= (int) ($p['month'] ?? 0) ?>">
      <td><strong><?= e((string) $p['name']) ?></strong></td>
      <td class="muted"><?= e((string) ($p['location'] ?? '')) ?: '–' ?></td>
      <td><?= ($p['status'] ?? '') === 'completed' ? e(project_date($p)) : '<span class="badge badge-teal">In progress</span>' ?></td>
      <td class="t-actions">
        <button type="button" class="btn btn-sm" data-project-edit>Edit</button>
        <form method="post" action="<?= e($self) ?>" class="inline" data-confirm="Delete this project from the website?">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($id) ?>">
          <button class="btn btn-sm btn-danger btn-icon" type="submit" aria-label="Delete project">
            <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/></svg>
          </button>
        </form>
      </td>
    </tr>
<?php };
?>
<section class="card">
  <div class="card-head">
    <h2>Ongoing <span class="muted">(<?= count($ongoing) ?>)</span></h2>
    <button type="button" class="btn btn-primary" data-project-add>
      <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg> Add project
    </button>
  </div>
  <?php if ($ongoing): ?>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Project</th><th>Location</th><th>Status</th><th></th></tr></thead>
      <tbody><?php foreach ($ongoing as $p) { $row($p); } ?></tbody>
    </table></div>
  <?php else: ?>
    <p class="muted">No ongoing projects. Use "Add project" to add one.</p>
  <?php endif; ?>
</section>

<section class="card">
  <div class="card-head"><h2>Completed <span class="muted">(<?= count($done) ?>)</span></h2></div>
  <?php if ($done): ?>
    <p class="muted small">Shown newest first, grouped by year, on the website.</p>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Project</th><th>Location</th><th>Completed</th><th></th></tr></thead>
      <tbody><?php foreach ($done as $p) { $row($p); } ?></tbody>
    </table></div>
  <?php else: ?>
    <p class="muted">No completed projects yet.</p>
  <?php endif; ?>
</section>

<?php modal_open('project-modal', 'Add project', '', 'sm'); ?>
  <form method="post" action="<?= e($self) ?>" data-project-form>
    <?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="">
    <label class="field"><span>Project name</span><input type="text" name="name" maxlength="160" required placeholder="e.g. Villa pool for Mr. Varghese"></label>
    <label class="field"><span>Location <i>(optional)</i></span><input type="text" name="location" maxlength="120" placeholder="e.g. Kakkanad, Kochi"></label>
    <fieldset class="seg" aria-label="Status">
      <label><input type="radio" name="status" value="ongoing" checked> <span>Ongoing</span></label>
      <label><input type="radio" name="status" value="completed"> <span>Completed</span></label>
    </fieldset>
    <div class="form-row form-row-2" data-completed-fields hidden>
      <label class="field"><span>Completed in <i>(month, optional)</i></span>
        <select name="month">
          <option value="0">–</option>
          <?php for ($m = 1; $m <= 12; $m++): ?><option value="<?= $m ?>"><?= e($months[$m]) ?></option><?php endfor; ?>
        </select></label>
      <label class="field"><span>Year</span>
        <select name="year">
          <?php for ($y = $thisYear + 1; $y >= 1995; $y--): ?><option value="<?= $y ?>"<?= $y === $thisYear ? ' selected' : '' ?>><?= $y ?></option><?php endfor; ?>
        </select></label>
    </div>
    <div class="modal-foot">
      <button type="button" class="btn" data-modal-close>Cancel</button>
      <button class="btn btn-primary" type="submit">Save project</button>
    </div>
  </form>
<?php modal_close(); ?>
<?php admin_foot();
