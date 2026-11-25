<section class="card">
  <div class="card-head">
    <div>
      <h2>Services</h2>
      <p>Manage services and their category.</p>
    </div>
    <a class="btn btn--small" href="index.php?page=back.service.create">+ Add a service</a>
  </div>

  <?php if ($error !== ''): ?>
    <p class="error"><?= $error ?></p>
  <?php endif; ?>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Status</th>
          <th>Category</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($services as $service): ?>
          <tr>
            <td><strong><?= $service['nomService'] ?></strong></td>
            <td>
              <span class="badge badge--<?= $service['status'] === 'open' ? 'open' : 'closed' ?>">
                <?= ucfirst($service['status']) ?>
              </span>
            </td>
            <td><span class="pill">#<?= $service['id_categorie'] ?></span></td>
            <td>
              <div class="actions">
                <a class="btn btn--ghost btn--small" href="index.php?page=back.service.edit&amp;id=<?= $service['id_service'] ?>">Edit</a>
                <form method="post" action="index.php?page=back.service.delete">
                  <input type="hidden" name="id" value="<?= $service['id_service'] ?>">
                  <button class="btn btn--danger btn--small" type="submit">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
