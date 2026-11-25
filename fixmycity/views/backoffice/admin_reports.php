<?php
// View: display only. Escape every value with htmlspecialchars().
// Expects: $reports (ReportModel[]), $categories, $filters, $pendingCount, $notice, $adminPage, $csrfToken
$statusLabels = ['pending' => 'Pending', 'in_progress' => 'In progress', 'resolved' => 'Resolved'];
// next allowed step of the workflow (no going back)
$nextStep = ['pending' => ['in_progress', 'Mark in progress'], 'in_progress' => ['resolved', 'Mark resolved']];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CityFix – Admin reports</title>
  <link rel="stylesheet" href="assets/css/admin.css">
  <script src="assets/js/script.js" defer></script>
</head>
<body>

  <?php require __DIR__ . '/partials/admin_sidebar.php'; ?>

  <main id="content">

    <header id="topbar">
      <h1>Reports moderation</h1>
    </header>

    <?php if ($notice): ?>
      <p class="notice notice-<?= e($notice['type']) ?>" role="status"><?= e($notice['text']) ?></p>
    <?php endif; ?>

    <!-- Search + filters -->
    <form id="search-form" action="index.php" method="get">
      <input type="hidden" name="action" value="admin_reports">

      <div>
        <label for="search">Search</label>
        <input type="search" id="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Address, description, category, department, user…">
      </div>

      <div>
        <label for="filter-type">Type</label>
        <select id="filter-type" name="type">
          <option value="">All types</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c->getId() ?>"<?= $filters['type'] === $c->getId() ? ' selected' : '' ?>><?= e($c->getName()) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label for="filter-status">Status</label>
        <select id="filter-status" name="status">
          <option value="">All statuses</option>
          <?php foreach ($statusLabels as $value => $label): ?>
            <option value="<?= e($value) ?>"<?= $filters['status'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label for="view">View</label>
        <select id="view" name="view">
          <?php foreach (['all' => 'View all', 'today' => 'Today', 'week' => 'This week', 'month' => 'This month'] as $value => $label): ?>
            <option value="<?= e($value) ?>"<?= $filters['view'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit">Filter</button>
    </form>

    <section id="reports">
      <header>
        <h2>Reports (<?= count($reports) ?>)</h2>
        <p>Each report is routed to the department of its category. Status only moves forward: pending → in progress → resolved.</p>
      </header>

      <?php if (!$reports): ?>
        <p class="empty">No report matches these filters.</p>
      <?php endif; ?>

      <div class="cards">
      <?php foreach ($reports as $r): ?>
        <?php $id = $r->getId(); $status = $r->getStatus(); ?>
        <article class="report-card" data-report-id="<?= $id ?>">
          <header>
            <h3><?= e($r->getCategoryName()) ?></h3>
            <span class="status badge-<?= e($status) ?>"><?= e($statusLabels[$status] ?? $status) ?></span>
          </header>

          <img src="<?= e($r->getPhoto()) ?>" alt="Photo of the reported problem" width="240" height="160" loading="lazy">

          <dl>
            <dt>Department</dt>
            <dd><?= e($r->getDepartmentName()) ?> (<?= e($r->getDepartmentEmail()) ?>)</dd>
            <dt>Location</dt>
            <dd><?= e(report_location_label($r)) ?></dd>
            <dt>Sent on</dt>
            <dd><time datetime="<?= e(date('Y-m-d', strtotime($r->getCreatedAt()))) ?>"><?= e(date('j M Y', strtotime($r->getCreatedAt()))) ?></time></dd>
            <?php if ($r->getDescription()): ?>
              <dt>Description</dt>
              <dd><?= e($r->getDescription()) ?></dd>
            <?php endif; ?>
          </dl>

          <footer>
            <p>
              <img src="assets/imgs/avatar.png" alt="" width="24" height="24">
              by <?= e($r->getUsername()) ?>
            </p>

            <?php if (isset($nextStep[$status])): ?>
              <form action="index.php?action=admin_report_status" method="post">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="report_id" value="<?= $id ?>">
                <input type="hidden" name="to" value="<?= e($nextStep[$status][0]) ?>">
                <button type="submit"><?= e($nextStep[$status][1]) ?></button>
              </form>
            <?php endif; ?>

            <details>
              <summary>More</summary>
              <ul>
                <li><a href="<?= e(report_map_url($r)) ?>" target="_blank" rel="noopener">Show on map</a></li>
                <li>
                  <form action="index.php?action=admin_report_delete" method="post" data-confirm="Delete this report and its photo?">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    <input type="hidden" name="report_id" value="<?= $id ?>">
                    <button type="submit">Delete</button>
                  </form>
                </li>
              </ul>
            </details>
          </footer>
        </article>
      <?php endforeach; ?>
      </div>

    </section>

  </main>

</body>
</html>
