<h2><?= $action === 'back.service.create' ? 'Add a service' : 'Edit service' ?></h2>

<?php foreach ($errors as $error): ?>
  <p class="error"><?= $error ?></p>
<?php endforeach; ?>

<form method="post" action="index.php?page=<?= $action ?>" data-validate>
  <p>
    <label for="nomService">Name</label>
    <input type="text" id="nomService" name="nomService" value="<?= $service['nomService'] ?? '' ?>" data-required>
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
        <option value="<?= $category['id_categorie'] ?>" <?= (string) ($service['id_categorie'] ?? '') === (string) $category['id_categorie'] ? 'selected' : '' ?>>
          <?= $category['nomCategorie'] ?>
        </option>
      <?php endforeach; ?>
    </select>
  </p>
  <p>
    <button type="submit">Save</button>
  </p>
</form>

<p><a href="index.php?page=back.services">Back</a></p>
