<?php
// View: display only. Escape every value with htmlspecialchars().
// Expects: $categories (CategoryModel[]), $errors, $old, $csrfToken, $user, $latestReports
$currentPage = 'report_form';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CityFix – Report a problem</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <?php require __DIR__ . '/partials/sidebar.php'; ?>

  <!-- ========== MIDDLE: MAIN (the form) ========== -->
  <main id="content">
    <header>
      <h1>Report a problem</h1>
      <p>Add a photo, tell us where it is, and we send it to the right department.</p>
    </header>

    <form id="report-form" action="index.php?action=report_create" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

      <?php if (isset($errors['form'])): ?>
        <p class="notice notice-error" role="alert"><?= e($errors['form']) ?></p>
      <?php endif; ?>

      <!-- 1. Photo -->
      <fieldset>
        <legend>1. Photo</legend>
        <label for="photo">Take or choose a picture</label>
        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" capture="environment" required>
        <p class="error" id="photo-error"<?= isset($errors['photo']) ? '' : ' hidden' ?>><?= e($errors['photo'] ?? '') ?></p>
      </fieldset>

      <!-- 2. Location -->
      <fieldset>
        <legend>2. Location</legend>

        <label for="address">Road, street or address (or enter coordinates)</label>
        <input type="text" id="address" name="address" maxlength="255" placeholder="Street, neighbourhood, landmark…" value="<?= e($old['address'] ?? '') ?>">
        <div id="coords-fields">
          <div class="coords">
            <div>
              <label for="latitude">Latitude</label>
              <input type="number" id="latitude" name="latitude" step="any" min="-90" max="90"
                     placeholder="36.8065" value="<?= e($old['latitude'] ?? '') ?>">
            </div>
            <div>
              <label for="longitude">Longitude</label>
              <input type="number" id="longitude" name="longitude" step="any" min="-180" max="180"
                     placeholder="10.1815" value="<?= e($old['longitude'] ?? '') ?>">
            </div>
          </div>
        </div>

        <p class="error" id="location-error"<?= isset($errors['location']) ? '' : ' hidden' ?>><?= e($errors['location'] ?? '') ?></p>
      </fieldset>

      <!-- 3. Category (from the database) + the department it is routed to -->
      <fieldset>
        <legend>3. Type of problem</legend>

        <label for="category">Category</label>
        <select id="category" name="category_id" required>
          <option value="" disabled<?= ($old['category_id'] ?? '') === '' ? ' selected' : '' ?>>Choose a category…</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c->getId() ?>"<?= (string) ($old['category_id'] ?? '') === (string) $c->getId() ? ' selected' : '' ?>>
              <?= e($c->getName()) ?> → <?= e($c->getDepartmentName()) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <p class="hint">The arrow shows the department that will handle your report.</p>
        <p class="error" id="category-error"<?= isset($errors['category']) ? '' : ' hidden' ?>><?= e($errors['category'] ?? '') ?></p>
      </fieldset>

      <!-- 4. Optional description -->
      <fieldset>
        <legend>4. Details (optional)</legend>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" maxlength="500" placeholder="Describe the problem…"><?= e($old['description'] ?? '') ?></textarea>
        <p class="hint">Up to 500 characters.</p>
        <p class="error" id="description-error"<?= isset($errors['description']) ? '' : ' hidden' ?>><?= e($errors['description'] ?? '') ?></p>
      </fieldset>

      <!-- user, status and created_at are set by the server, never by the citizen -->
      <div class="actions">
        <button type="submit">Send report</button>
        <button type="reset">Clear</button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/partials/right_panel.php'; ?>

</body>
</html>
