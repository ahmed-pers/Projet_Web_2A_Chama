<h2>Counters - Back Office</h2>

<p><a href="index.php?page=back.counter.create">Add a counter</a></p>

<table>
  <tr>
    <th>Number</th>
    <th>Location</th>
    <th>Status</th>
    <th>Actions</th>
  </tr>
  <?php foreach ($counters as $counter): ?>
    <tr>
      <td><?= e($counter['number']) ?></td>
      <td><?= e($counter['location']) ?></td>
      <td><?= e($counter['status']) ?></td>
      <td>
        <a href="index.php?page=back.counter.edit&amp;id=<?= e($counter['id']) ?>">Edit</a>
        <form method="post" action="index.php?page=back.counter.delete">
          <input type="hidden" name="id" value="<?= e($counter['id']) ?>">
          <button type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<p><a href="index.php?page=back.services">Manage services</a></p>
