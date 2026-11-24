<h2><?= $action === 'back.categorie.create' ? 'Add a category' : 'Edit category' ?></h2>

<?php foreach ($errors as $error): ?>
  <p><strong><?= $error ?></strong></p>
<?php endforeach; ?>

<form method="post" action="index.php?page=<?= $action ?>">
  <p>
    <label for="nomCategorie">Name</label>
    <input type="text" id="nomCategorie" name="nomCategorie" value="<?= $category['nomCategorie'] ?? '' ?>">
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
