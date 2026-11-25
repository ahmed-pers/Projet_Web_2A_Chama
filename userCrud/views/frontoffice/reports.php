<?php
// View: display only. Escape every value with htmlspecialchars().
// Expects: $reports (ReportModel[]), $mine (bool), $currentUserId, $notice, $csrfToken, $user, $latestReports
$currentPage = $mine ? 'mine' : 'reports';
$statusLabels = ['pending' => 'Pending', 'in_progress' => 'In progress', 'resolved' => 'Resolved'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CityFix – <?= $mine ? 'My reports' : 'All reports' ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <?php require __DIR__ . '/partials/sidebar.php'; ?>

  <main id="content">
    <header>
      <h1><?= $mine ? 'My reports' : 'All reports' ?></h1>
      <p><?= $mine ? 'Follow what happened to the problems you reported.' : 'Problems reported by all citizens.' ?></p>
    </header>

    <?php if ($notice): ?>
      <p class="notice notice-<?= e($notice['type']) ?>" role="status"><?= e($notice['text']) ?></p>
    <?php endif; ?>

    <?php if (!$reports): ?>
      <p class="empty">Nothing here yet. <a class="see-all" href="index.php?action=report_form">Report a problem</a></p>
    <?php else: ?>
      <ul class="report-list">
        <?php foreach ($reports as $r): ?>
          <?php $own = $r->getUserId() === $currentUserId; $canEdit = $own && $r->getStatus() === 'pending'; ?>
          <li class="report-item">
            <img src="<?= e($r->getPhoto()) ?>" alt="Photo of the reported problem" width="120" height="90" loading="lazy">
            <div>
              <h2><?= e($r->getCategoryName()) ?>
                <span class="badge badge-<?= e($r->getStatus()) ?>"><?= e($statusLabels[$r->getStatus()] ?? $r->getStatus()) ?></span>
              </h2>
              <p class="hint">Handled by <?= e($r->getDepartmentName()) ?> · <?= e(report_location_label($r)) ?> ·
                <time datetime="<?= e(date('Y-m-d', strtotime($r->getCreatedAt()))) ?>"><?= e(date('j M Y', strtotime($r->getCreatedAt()))) ?></time>
                <?php if (!$mine): ?> · by <?= e($r->getUsername()) ?><?php endif; ?>
              </p>
              <?php if ($r->getDescription()): ?><p><?= e($r->getDescription()) ?></p><?php endif; ?>

              <?php if ($canEdit): ?>
                <div class="item-actions">
                  <a class="btn-link" href="index.php?action=report_edit&amp;id=<?= $r->getId() ?>">Edit</a>
                  <form action="index.php?action=report_delete" method="post" data-confirm="Delete this report?">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    <input type="hidden" name="report_id" value="<?= $r->getId() ?>">
                    <button type="submit" class="btn-link">Delete</button>
                  </form>
                </div>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </main>

  <?php require __DIR__ . '/partials/right_panel.php'; ?>

</body>
</html>
