<section class="card">
  <div class="card-head">
    <div>
      <h2>Tickets</h2>
      <p>All tickets reserved from the Front Office.</p>
    </div>
    <a class="btn btn--ghost btn--small" href="index.php?page=front.tickets">Reserve a ticket</a>
  </div>

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
