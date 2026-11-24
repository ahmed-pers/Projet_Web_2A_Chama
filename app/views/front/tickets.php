<h2>My Tickets</h2>

<h3>Get a ticket</h3>

<?php foreach ($errors as $error): ?>
  <p><strong><?= e($error) ?></strong></p>
<?php endforeach; ?>

<form method="post" action="index.php?page=front.tickets" data-validate>
  <p>
    <label for="id_Ticket">Ticket number</label>
    <input type="number" id="id_Ticket" name="id_Ticket" value="<?= e($ticket['id_Ticket'] ?? '') ?>" data-required>
  </p>
  <p>
    <label for="id_client">Client number</label>
    <input type="number" id="id_client" name="id_client" value="<?= e($ticket['id_client'] ?? '') ?>" data-required>
  </p>
  <p>
    <label for="id_service">Service</label>
    <select id="id_service" name="id_service" data-required>
      <?php foreach ($services as $service): ?>
        <option value="<?= e($service['id_service']) ?>" <?= (string) ($ticket['id_service'] ?? '') === (string) $service['id_service'] ? 'selected' : '' ?>>
          <?= e($service['nomService']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </p>
  <p>
    <label for="date">Date</label>
    <input type="date" id="date" name="date" value="<?= e($ticket['date'] ?? '') ?>" data-required>
  </p>
  <p>
    <label for="time">Time</label>
    <input type="time" id="time" name="time" value="<?= e($ticket['time'] ?? '') ?>" data-required>
  </p>
  <p>
    <button type="submit">Get my ticket</button>
  </p>
</form>

<h3>All tickets</h3>
<table>
  <tr>
    <th>Ticket</th>
    <th>Client</th>
    <th>Service</th>
    <th>Category</th>
    <th>Date</th>
    <th>Time</th>
  </tr>
  <?php foreach ($tickets as $item): ?>
    <tr>
      <td><?= e($item['id_Ticket']) ?></td>
      <td><?= e($item['id_client']) ?></td>
      <td><?= e($item['nomService']) ?></td>
      <td><?= e($item['nomCategorie']) ?></td>
      <td><?= e($item['date']) ?></td>
      <td><?= e($item['time']) ?></td>
    </tr>
  <?php endforeach; ?>
</table>
