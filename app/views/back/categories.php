<h2>Categories - Back Office</h2>

<p><a href="index.php?page=back.categorie.create">Add a category</a></p>

<table>
  <tr>
    <th>Id</th>
    <th>Name</th>
    <th>Description</th>
    <th>Services</th>
    <th>Actions</th>
  </tr>
  <?php foreach ($categories as $category): ?>
    <tr>
      <td><?= e($category['id_categorie']) ?></td>
      <td><?= e($category['nomCategorie']) ?></td>
      <td><?= e($category['description']) ?></td>
      <td><?= e($category['service_count']) ?></td>
      <td>
        <a href="index.php?page=back.categorie.edit&amp;id=<?= e($category['id_categorie']) ?>">Edit</a>
        <form method="post" action="index.php?page=back.categorie.delete">
          <input type="hidden" name="id" value="<?= e($category['id_categorie']) ?>">
          <button type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<p><a href="index.php?page=back.services">Manage services</a></p>
