<?php
// View: display only. Escape every value with htmlspecialchars().
// Expects: $categories (CategoryModel[]), $departments (array of id/name/contact_email), $pendingCount, $notice, $adminPage, $csrfToken
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CityFix – Admin categories</title>
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

  <?php require __DIR__ . '/partials/admin_sidebar.php'; ?>

  <main id="content">

    <header id="topbar">
      <h1>Categories</h1>
    </header>

    <p class="hint">A category is a type of problem. Its department receives the reports (automatic routing).</p>

    <?php if ($notice): ?>
      <p class="notice notice-<?= e($notice['type']) ?>" role="status"><?= e($notice['text']) ?></p>
    <?php endif; ?>

    <!-- Create -->
    <form id="category-add" action="index.php?action=admin_category_save" method="post">
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
      <h2>Add a category</h2>
      <div>
        <label for="new-name">Name</label>
        <input type="text" id="new-name" name="name" maxlength="100" required>
      </div>
      <div>
        <label for="new-department">Department</label>
        <select id="new-department" name="department_id" required>
          <option value="" disabled selected>Choose…</option>
          <?php foreach ($departments as $d): ?>
            <option value="<?= (int) $d['id'] ?>"><?= e($d['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit">Add</button>
    </form>

    <!-- Read / Update / Delete -->
    <section id="categories">
      <h2>All categories (<?= count($categories) ?>)</h2>

      <?php if (!$categories): ?>
        <p class="empty">No category yet.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr><th>Name</th><th>Department</th><th>Contact</th><th>Reports</th><th>Actions</th></tr>
          </thead>
          <tbody>
          <?php foreach ($categories as $c): ?>
            <?php $id = $c->getId(); ?>
            <tr>
              <td>
                <label class="sr-only" for="name-<?= $id ?>">Name</label>
                <input type="text" id="name-<?= $id ?>" name="name" form="edit-<?= $id ?>" value="<?= e($c->getName()) ?>" maxlength="100" required>
              </td>
              <td>
                <label class="sr-only" for="dept-<?= $id ?>">Department</label>
                <select id="dept-<?= $id ?>" name="department_id" form="edit-<?= $id ?>" required>
                  <?php foreach ($departments as $d): ?>
                    <option value="<?= (int) $d['id'] ?>"<?= (int) $d['id'] === $c->getDepartmentId() ? ' selected' : '' ?>><?= e($d['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td><?= e($c->getDepartmentEmail()) ?></td>
              <td><?= $c->getReportCount() ?></td>
              <td>
                <form id="edit-<?= $id ?>" action="index.php?action=admin_category_save" method="post">
                  <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                  <input type="hidden" name="id" value="<?= $id ?>">
                  <button type="submit">Save</button>
                </form>
                <form action="index.php?action=admin_category_delete" method="post" data-confirm="Delete this category?">
                  <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                  <input type="hidden" name="id" value="<?= $id ?>">
                  <button type="submit"<?= $c->getReportCount() > 0 ? ' disabled title="Used by reports"' : '' ?>>Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>

  </main>

</body>
</html>
