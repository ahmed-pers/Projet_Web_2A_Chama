<h2>Tickets - Back Office</h2>

<p>Tickets are created in the Front Office. This page shows all tickets.</p>

<table>
  <tr>
    <th>Ticket</th>
    <th>Client</th>
    <th>Service</th>
    <th>Category</th>
    <th>Date</th>
    <th>Time</th>
  </tr>
  <?php foreach ($tickets as $ticket): ?>
    <tr>
      <td><?= e($ticket['id_Ticket']) ?></td>
      <td><?= e($ticket['id_client']) ?></td>
      <td><?= e($ticket['nomService']) ?></td>
      <td><?= e($ticket['nomCategorie']) ?></td>
      <td><?= e($ticket['date']) ?></td>
      <td><?= e($ticket['time']) ?></td>
    </tr>
  <?php endforeach; ?>
</table>
