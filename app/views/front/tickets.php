<section class="card">
  <h2>Reserve a ticket</h2>
  <p>Enter your client number and choose a service.</p>

  <?php foreach ($errors as $error): ?>
    <p class="error"><?= $error ?></p>
  <?php endforeach; ?>

  <form class="form" method="post" action="index.php?page=front.tickets" data-validate>
    <p>
      <label for="id_client">Client number</label>
      <input type="number" id="id_client" name="id_client" value="<?= $client ?>" data-required>
    </p>
    <p>
      <label for="id_service">Service</label>
      <select id="id_service" name="id_service" data-required>
        <?php foreach ($services as $service): ?>
          <option value="<?= $service['id_service'] ?>" <?= (string) $serviceId === (string) $service['id_service'] ? 'selected' : '' ?>>
            <?= $service['nomService'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </p>
    <p><button class="btn" type="submit">Reserve ticket</button></p>
  </form>
</section>

<section class="card">
  <h2>All tickets</h2>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Ticket</th>
          <th>Client</th>
          <th>Service</th>
          <th>Date</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tickets as $ticket): ?>
          <tr>
            <td><span class="pill">#<?= $ticket['id_Ticket'] ?></span></td>
            <td><?= $ticket['id_client'] ?></td>
            <td><span class="pill">#<?= $ticket['id_service'] ?></span></td>
            <td><?= $ticket['date'] ?></td>
            <td><?= $ticket['time'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
