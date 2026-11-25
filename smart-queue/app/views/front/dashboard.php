<section class="hero">
  <div>
    <span class="hero-tag">Smart Queue</span>
    <h2>Today's Queue</h2>
    <p>Reserve a ticket, then follow the queue from one simple dashboard.</p>
  </div>
  <a class="btn btn--light" href="index.php?page=front.tickets">Reserve a ticket →</a>
</section>

<div class="stats">
  <div class="stat">
    <span class="stat-label">Services</span>
    <span class="stat-value"><?= count($services) ?></span>
  </div>
  <div class="stat stat--teal">
    <span class="stat-label">Tickets today</span>
    <span class="stat-value"><?= count($tickets) ?></span>
  </div>
</div>

<section class="card">
  <div class="card-head">
    <h2>Available services</h2>
    <a class="btn btn--ghost btn--small" href="index.php?page=front.services">View all</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Id</th>
          <th>Service</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($services as $service): ?>
          <tr>
            <td><span class="pill">#<?= $service['id_service'] ?></span></td>
            <td><?= $service['nomService'] ?></td>
            <td>
              <span class="badge badge--<?= $service['status'] === 'open' ? 'open' : 'closed' ?>">
                <?= ucfirst($service['status']) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
