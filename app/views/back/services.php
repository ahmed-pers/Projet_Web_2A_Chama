<h2>Services - Back Office</h2>

<p><a href="index.php?page=back.service.create">Add a service</a></p>

<table>
  <tr>
    <th>Name</th>
    <th>Description</th>
    <th>Average time</th>
    <th>Status</th>
    <th>Counters</th>
    <th>Actions</th>
  </tr>
  <?php foreach ($services as $service): ?>
    <tr>
      <td><?= e($service['name']) ?></td>
      <td><?= e($service['description']) ?></td>
      <td><?= e($service['average_time']) ?> min</td>
      <td><?= e($service['status']) ?></td>
      <td><?= e($service['counter_count']) ?></td>
      <td>
        <a href="index.php?page=back.service.edit&amp;id=<?= e($service['id']) ?>">Edit</a>
        <a href="index.php?page=back.assign&amp;id=<?= e($service['id']) ?>">Assign counters</a>
        <form method="post" action="index.php?page=back.service.delete">
          <input type="hidden" name="id" value="<?= e($service['id']) ?>">
          <button type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<p><a href="index.php?page=back.counters">Manage counters</a></p>
