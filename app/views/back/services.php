<h2>Services</h2>

<?php if ($error !== ''): ?>
  <p><strong><?= $error ?></strong></p>
<?php endif; ?>

<p><a href="index.php?page=back.service.create">Add a service</a></p>

<table>
  <tr>
    <th>Name</th>
    <th>Status</th>
    <th>Category id</th>
    <th>Actions</th>
  </tr>
  <?php foreach ($services as $service): ?>
    <tr>
      <td><?= $service['nomService'] ?></td>
      <td><?= $service['status'] ?></td>
      <td><?= $service['id_categorie'] ?></td>
      <td>
        <a href="index.php?page=back.service.edit&amp;id=<?= $service['id_service'] ?>">Edit</a>
        <form method="post" action="index.php?page=back.service.delete">
          <input type="hidden" name="id" value="<?= $service['id_service'] ?>">
          <button type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
