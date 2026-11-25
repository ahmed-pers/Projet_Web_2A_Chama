<section class="card">
  <div class="card-head">
    <h2>Services</h2>
    <a class="btn btn--ghost btn--small" href="index.php?page=front.tickets">Reserve</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Id</th>
          <th>Service</th>
          <th>Status</th>
          <th>Category</th>
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
            <td><span class="pill">#<?= $service['id_categorie'] ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
