<h2>Our Services</h2>

<p>Services with their category (SQL join between SERVICE and CATEGORIE).</p>

<table>
  <tr>
    <th>Id</th>
    <th>Service</th>
    <th>Category</th>
    <th>Status</th>
  </tr>
  <?php foreach ($services as $service): ?>
    <tr>
      <td><?= e($service['id_service']) ?></td>
      <td><?= e($service['nomService']) ?></td>
      <td><?= e($service['nomCategorie']) ?></td>
      <td><?= e($service['status']) ?></td>
    </tr>
  <?php endforeach; ?>
</table>

<p><a href="index.php?page=front.tickets">Get a ticket</a></p>
