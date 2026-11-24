<h2><?= $action === 'back.categorie.create' ? 'Add a category' : 'Edit category' ?></h2>

<?php foreach ($errors as $error): ?>
  <p class="error"><?= $error ?></p>
<?php endforeach; ?>

<form method="post" action="index.php?page=<?= $action ?>" data-validate>
  <p>
    <label for="nomCategorie">Name</label>
    <input type="text" id="nomCategorie" name="nomCategorie" value="<?= $category['nomCategorie'] ?? '' ?>" data-required>
  </p>
  <p>
    <label for="description">Description</label>
    <input type="text" id="description" name="description" value="<?= $category['description'] ?? '' ?>">
  </p>
  <p>
    <button type="submit">Save</button>
  </p>
</form>

<p><a href="index.php?page=back.categories">Back</a></p>
