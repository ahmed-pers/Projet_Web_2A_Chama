<?php require __DIR__ . '/partials/header.php'; ?>

<?php $isEdit = $item !== null; ?>
<section class="panel form-panel">
  <h1><?= $isEdit ? 'Edit item' : 'Report an item' ?></h1>

  <?php foreach ($errors as $error): ?>
    <p class="notice error" role="alert"><?= e($error) ?></p>
  <?php endforeach; ?>

  <form method="post" enctype="multipart/form-data" class="form" action="index.php?action=<?= $isEdit ? 'item_update' : 'item_create' ?>">
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
    <?php if ($isEdit): ?>
      <input type="hidden" name="id_item" value="<?= (int) $form['id_item'] ?>">
    <?php endif; ?>

    <label>Type of report
      <?php foreach (['lost' => 'I lost an item', 'found' => 'I found an item'] as $value => $label): ?>
        <span class="radio">
          <input type="radio" name="type" value="<?= e($value) ?>"<?= ($form['type'] ?? 'lost') === $value ? ' checked' : '' ?>>
          <?= e($label) ?>
        </span>
      <?php endforeach; ?>
    </label>

    <label>Item name *
      <input type="text" name="item_name" maxlength="150" required value="<?= e($form['item_name'] ?? '') ?>" placeholder="e.g. Blue backpack">
    </label>

    <div class="form-row">
      <label>Category *
        <select name="id_category" required>
          <option value="">Choose a category</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= $category->getId() ?>"<?= (string) ($form['id_category'] ?? '') === (string) $category->getId() ? ' selected' : '' ?>>
              <?= e($category->getName()) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Date lost / found *
        <input type="date" name="date_found" required value="<?= e($form['date_found'] ?? '') ?>">
      </label>
    </div>

    <label>Description
      <textarea name="description" rows="4" maxlength="500" placeholder="Colour, brand, distinguishing marks..."><?= e($form['description'] ?? '') ?></textarea>
    </label>

    <label>Photo<?= $isEdit ? ' (leave empty to keep the current one)' : '' ?>
      <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
    </label>

    <?php if (!empty($form['image_url'])): ?>
      <p><img class="item-photo" src="<?= e($form['image_url']) ?>" alt="Current item photo"></p>
    <?php endif; ?>

    <p class="actions">
      <button class="button" type="submit"><?= $isEdit ? 'Save changes' : 'Submit report' ?></button>
      <a href="index.php?action=<?= $isEdit ? 'my_items' : 'items' ?>">Cancel</a>
    </p>
  </form>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
