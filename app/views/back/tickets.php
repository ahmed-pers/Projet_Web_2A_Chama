<h2>Tickets</h2>

<table>
  <tr>
    <th>Ticket</th>
    <th>Client</th>
    <th>Service id</th>
    <th>Date</th>
    <th>Time</th>
  </tr>
  <?php foreach ($tickets as $ticket): ?>
    <tr>
      <td><?= $ticket['id_Ticket'] ?></td>
      <td><?= $ticket['id_client'] ?></td>
      <td><?= $ticket['id_service'] ?></td>
      <td><?= $ticket['date'] ?></td>
      <td><?= $ticket['time'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>
