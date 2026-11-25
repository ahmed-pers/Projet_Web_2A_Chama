<?php require __DIR__ . '/partials/header.php'; ?>

<header class="page-header">
  <div>
    <h1>Categories</h1>
    <p>Categories used when reporting an item.</p>
  </div>
</header>

<section class="panel">
  <h2>All categories</h2>
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Name</th><th>Description</th><th>Items</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($categories as $category): ?>
          <tr>
            <td><?= e($category->getName()) ?></td>
            <td><?= e($category->getDescription() ?: '-') ?></td>
            <td><?= $category->getItemCount() ?></td>
            <td>
              <form method="post" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="id_category" value="<?= $category->getId() ?>">
                <input type="text" name="name" maxlength="100" value="<?= e($category->getName()) ?>" required>
                <input type="text" name="description" maxlength="255" value="<?= e($category->getDescription() ?? '') ?>">
                <button type="submit" formaction="index.php?action=admin_category_save">Save</button>
              </form>
              <form method="post" class="inline-form" data-confirm="Delete this category?">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="id_category" value="<?= $category->getId() ?>">
                <button class="link-danger" type="submit" formaction="index.php?action=admin_category_delete">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$categories): ?><tr><td colspan="4">No categories yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="panel form-panel">
  <h2>Add a category</h2>
  <form method="post" action="index.php?action=admin_category_save" class="form">
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
    <label>Name *<input type="text" name="name" maxlength="100" required></label>
    <label>Description<input type="text" name="description" maxlength="255"></label>
    <p><button class="button" type="submit">Create category</button></p>
  </form>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
