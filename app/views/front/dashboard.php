<h2>Today's Queue</h2>

<p>Don't wait in line. Wait wherever you want.</p>

<p><a href="index.php?page=front.tickets">Reserve a ticket</a></p>

<ul>
  <li>Categories: <?= e(count($categories)) ?></li>
  <li>Services: <?= e(count($services)) ?></li>
  <li>Tickets: <?= e($ticketCount) ?></li>
</ul>

<h3>Open services</h3>
<table>
  <tr>
    <th>Service</th>
    <th>Category</th>
  </tr>
  <?php foreach ($services as $service): ?>
    <?php if ($service['status'] === 'open'): ?>
      <tr>
        <td><?= e($service['nomService']) ?></td>
        <td><?= e($service['nomCategorie']) ?></td>
      </tr>
    <?php endif; ?>
  <?php endforeach; ?>
</table>

<p><a href="index.php?page=front.services">See all services</a></p>
