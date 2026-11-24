<h2>Services</h2>

<table>
  <tr>
    <th>Id</th>
    <th>Service</th>
    <th>Status</th>
    <th>Category id</th>
  </tr>
  <?php foreach ($services as $service): ?>
    <tr>
      <td><?= $service['id_service'] ?></td>
      <td><?= $service['nomService'] ?></td>
      <td><?= $service['status'] ?></td>
      <td><?= $service['id_categorie'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>
