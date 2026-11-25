<section class="card">
  <h2><?= $action === 'back.categorie.create' ? 'Add a category' : 'Edit category' ?></h2>
  <p>Fill in the name and a short description.</p>

  <?php foreach ($errors as $error): ?>
    <p class="error"><?= $error ?></p>
  <?php endforeach; ?>

  <form class="form" method="post" action="index.php?page=<?= $action ?>" data-validate>
    <p>
      <label for="nomCategorie">Name</label>
      <input type="text" id="nomCategorie" name="nomCategorie" value="<?= $category['nomCategorie'] ?? '' ?>" data-required>
    </p>
    <p>
      <label for="description">Description</label>
      <input type="text" id="description" name="description" value="<?= $category['description'] ?? '' ?>">
    </p>
    <p>
      <button class="btn" type="submit">Save category</button>
      <a class="btn btn--ghost" href="index.php?page=back.categories">Back</a>
    </p>
  </form>
</section>
