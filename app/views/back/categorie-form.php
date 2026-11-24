<h2><?= isset($category['id_categorie']) && $category['id_categorie'] !== '' ? 'Edit category' : 'Add a category' ?></h2>

<?php foreach ($errors as $error): ?>
  <p><strong><?= e($error) ?></strong></p>
<?php endforeach; ?>

<form method="post" action="index.php?page=<?= e($action) ?>" data-validate>
  <p>
    <label for="id_categorie">Category number</label>
    <input type="number" id="id_categorie" name="id_categorie" value="<?= e($category['id_categorie'] ?? '') ?>" <?= isset($category['id_categorie']) && $category['id_categorie'] !== '' ? 'readonly' : '' ?> data-required>
  </p>
  <p>
    <label for="nomCategorie">Name</label>
    <input type="text" id="nomCategorie" name="nomCategorie" value="<?= e($category['nomCategorie'] ?? '') ?>" data-required>
  </p>
  <p>
    <label for="description">Description</label>
    <input type="text" id="description" name="description" value="<?= e($category['description'] ?? '') ?>">
  </p>
  <p>
    <button type="submit">Save</button>
  </p>
</form>

<p><a href="index.php?page=back.categories">Back to categories</a></p>
