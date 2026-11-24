<h2>Today's Queue</h2>

<p>Don't wait in line. Wait wherever you want.</p>

<h3>Services</h3>
<table>
  <tr>
    <th>Service</th>
    <th>Average time</th>
    <th>Status</th>
  </tr>
  <?php foreach ($services as $service): ?>
    <tr>
      <td><?= e($service['name']) ?></td>
      <td><?= e($service['average_time']) ?> min</td>
      <td><?= e($service['status']) ?></td>
    </tr>
  <?php endforeach; ?>
</table>

<h3>Open counters</h3>
<ul>
  <?php foreach ($counters as $counter): ?>
    <?php if ($counter['status'] === 'open'): ?>
      <li><?= e($counter['number']) ?> - <?= e($counter['location']) ?></li>
    <?php endif; ?>
  <?php endforeach; ?>
</ul>

<p><a href="index.php?page=front.services">See services and their counters</a></p>
