<h2><?= $action === 'back.service.create' ? 'Add a service' : 'Edit service' ?></h2>

<?php foreach ($errors as $error): ?>
  <p><strong><?= e($error) ?></strong></p>
<?php endforeach; ?>

<form method="post" action="index.php?page=<?= e($action) ?>" data-validate>
  <p>
    <label for="nomService">Service name</label>
    <input type="text" id="nomService" name="nomService" value="<?= e($service['nomService'] ?? '') ?>" data-required>
  </p>
  <p>
    <label for="status">Status</label>
    <select id="status" name="status">
      <option value="open" <?= ($service['status'] ?? 'open') === 'open' ? 'selected' : '' ?>>Open</option>
      <option value="closed" <?= ($service['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
    </select>
  </p>
  <p>
    <label for="id_categorie">Category</label>
    <select id="id_categorie" name="id_categorie" data-required>
      <?php foreach ($categories as $category): ?>
        <option value="<?= e($category['id_categorie']) ?>" <?= (string) ($service['id_categorie'] ?? '') === (string) $category['id_categorie'] ? 'selected' : '' ?>>
          <?= e($category['nomCategorie']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </p>
  <p>
    <button type="submit">Save</button>
  </p>
</form>

<p><a href="index.php?page=back.services">Back to services</a></p>
