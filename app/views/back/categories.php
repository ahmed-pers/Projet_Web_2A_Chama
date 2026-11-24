<h2>Categories</h2>

<?php if ($error !== ''): ?>
  <p><strong><?= $error ?></strong></p>
<?php endif; ?>

<p><a href="index.php?page=back.categorie.create">Add a category</a></p>

<table>
  <tr>
    <th>Name</th>
    <th>Description</th>
    <th>Actions</th>
  </tr>
  <?php foreach ($categories as $category): ?>
    <tr>
      <td><?= $category['nomCategorie'] ?></td>
      <td><?= $category['description'] ?></td>
      <td>
        <a href="index.php?page=back.categorie.edit&amp;id=<?= $category['id_categorie'] ?>">Edit</a>
        <form method="post" action="index.php?page=back.categorie.delete">
          <input type="hidden" name="id" value="<?= $category['id_categorie'] ?>">
          <button type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
