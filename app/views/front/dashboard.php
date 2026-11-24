<h2>Today's Queue</h2>

<p><a href="index.php?page=front.tickets">Reserve a ticket</a></p>

<ul>
  <li>Services: <?= count($services) ?></li>
  <li>Tickets: <?= count($tickets) ?></li>
</ul>

<table>
  <tr>
    <th>Id</th>
    <th>Service</th>
    <th>Status</th>
  </tr>
  <?php foreach ($services as $service): ?>
    <tr>
      <td><?= $service['id_service'] ?></td>
      <td><?= $service['nomService'] ?></td>
      <td><?= $service['status'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>
