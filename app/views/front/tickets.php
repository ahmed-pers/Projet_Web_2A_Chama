<h2>Reserve a ticket</h2>

<?php foreach ($errors as $error): ?>
  <p><strong><?= $error ?></strong></p>
<?php endforeach; ?>

<form method="post" action="index.php?page=front.tickets">
  <p>
    <label for="id_client">Client number</label>
    <input type="number" id="id_client" name="id_client" value="<?= $client ?>">
  </p>
  <p>
    <label for="id_service">Service</label>
    <select id="id_service" name="id_service">
      <?php foreach ($services as $service): ?>
        <option value="<?= $service['id_service'] ?>" <?= (string) $serviceId === (string) $service['id_service'] ? 'selected' : '' ?>>
          <?= $service['nomService'] ?>
        </option>
      <?php endforeach; ?>
    </select>
  </p>
  <p>
    <button type="submit">Reserve</button>
  </p>
</form>

<h3>All tickets</h3>
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
