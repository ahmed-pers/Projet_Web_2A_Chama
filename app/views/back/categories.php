<section class="card">
  <div class="card-head">
    <div>
      <h2>Categories</h2>
      <p>Manage the categories used by your services.</p>
    </div>
    <a class="btn btn--small" href="index.php?page=back.categorie.create">+ Add a category</a>
  </div>

  <?php if ($error !== ''): ?>
    <p class="error"><?= $error ?></p>
  <?php endif; ?>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $category): ?>
          <tr>
            <td><strong><?= $category['nomCategorie'] ?></strong></td>
            <td><?= $category['description'] ?></td>
            <td>
              <div class="actions">
                <a class="btn btn--ghost btn--small" href="index.php?page=back.categorie.edit&amp;id=<?= $category['id_categorie'] ?>">Edit</a>
                <form method="post" action="index.php?page=back.categorie.delete">
                  <input type="hidden" name="id" value="<?= $category['id_categorie'] ?>">
                  <button class="btn btn--danger btn--small" type="submit">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
