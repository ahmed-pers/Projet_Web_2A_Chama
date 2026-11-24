<h2>Our Services</h2>

<p>Services and the counters that serve them.</p>

<ul>
  <?php foreach ($services as $service): ?>
    <li>
      <strong><?= e($service['name']) ?></strong>
      - <?= e($service['average_time']) ?> min
      - <?= e($service['status']) ?>
      <?php if (empty($service['counters'])): ?>
        <p>No counter assigned yet.</p>
      <?php else: ?>
        <ul>
          <?php foreach ($service['counters'] as $counter): ?>
            <li><?= e($counter['number']) ?> (<?= e($counter['status']) ?>)</li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>

<p><a href="index.php?page=front">Back to dashboard</a></p>
