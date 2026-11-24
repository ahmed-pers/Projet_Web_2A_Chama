<h2>Services - Back Office</h2>

<p><a href="index.php?page=back.service.create">Add a service</a></p>

<table>
  <tr>
    <th>Id</th>
    <th>Service</th>
    <th>Category</th>
    <th>Status</th>
    <th>Tickets</th>
    <th>Actions</th>
  </tr>
  <?php foreach ($services as $service): ?>
    <tr>
      <td><?= e($service['id_service']) ?></td>
      <td><?= e($service['nomService']) ?></td>
      <td><?= e($service['nomCategorie']) ?></td>
      <td><?= e($service['status']) ?></td>
      <td><?= e($service['ticket_count']) ?></td>
      <td>
        <a href="index.php?page=back.service.edit&amp;id=<?= e($service['id_service']) ?>">Edit</a>
        <form method="post" action="index.php?page=back.service.delete">
          <input type="hidden" name="id" value="<?= e($service['id_service']) ?>">
          <button type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<p><a href="index.php?page=back.categories">Manage categories</a></p>
