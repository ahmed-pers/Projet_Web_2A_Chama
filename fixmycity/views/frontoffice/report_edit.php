<?php
// View: display only. Escape every value with htmlspecialchars().
// Expects: $report (ReportModel), $categories, $errors, $old, $csrfToken, $user, $latestReports
$currentPage = 'mine';
$selectedCat = (string) ($old['category_id'] ?? $report->getCategoryId());
$descValue   = $old['description'] ?? ($report->getDescription() ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CityFix – Edit my report</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/script.js" defer></script>
</head>
<body>

  <?php require __DIR__ . '/partials/sidebar.php'; ?>

  <main id="content">
    <header>
      <h1>Edit my report</h1>
      <p>You can change the category and the description while the report is pending.</p>
    </header>

    <form id="edit-form" class="simple-form" action="index.php?action=report_update" method="post" novalidate>
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
      <input type="hidden" name="report_id" value="<?= $report->getId() ?>">

      <img class="edit-photo" src="<?= e($report->getPhoto()) ?>" alt="Photo of the reported problem">
      <p class="hint">Location: <?= e(report_location_label($report)) ?></p>

      <label for="category">Category</label>
      <select id="category" name="category_id" required>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c->getId() ?>"<?= $selectedCat === (string) $c->getId() ? ' selected' : '' ?>>
            <?= e($c->getName()) ?> → <?= e($c->getDepartmentName()) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <p class="error"<?= isset($errors['category']) ? '' : ' hidden' ?>><?= e($errors['category'] ?? '') ?></p>

      <label for="description">Description</label>
      <textarea id="description" name="description" rows="4" maxlength="500"><?= e($descValue) ?></textarea>
      <p class="hint">Up to 500 characters.</p>
      <p class="error"<?= isset($errors['description']) ? '' : ' hidden' ?>><?= e($errors['description'] ?? '') ?></p>

      <div class="actions">
        <button type="submit">Save changes</button>
        <a class="btn-link" href="index.php?action=reports&amp;mine=1">Cancel</a>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/partials/right_panel.php'; ?>

</body>
</html>
