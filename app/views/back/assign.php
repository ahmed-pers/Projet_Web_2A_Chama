<h2>Assign counters to <?= e($service['name']) ?></h2>

<?php if ($error !== ''): ?>
  <p><strong><?= e($error) ?></strong></p>
<?php endif; ?>

<p>Rules: a closed counter cannot be assigned. A counter can serve at most <?= e(MAX_SERVICES_PER_COUNTER) ?> services.</p>

<form method="post" action="index.php?page=back.assign&amp;id=<?= e($service['id']) ?>">
  <?php foreach ($counters as $counter): ?>
    <p>
      <input
        type="checkbox"
        id="counter<?= e($counter['id']) ?>"
        name="counter_ids[]"
        value="<?= e($counter['id']) ?>"
        <?= in_array((int) $counter['id'], $selected, true) ? 'checked' : '' ?>
      >
      <label for="counter<?= e($counter['id']) ?>">
        <?= e($counter['number']) ?> - <?= e($counter['status']) ?>
      </label>
    </p>
  <?php endforeach; ?>
  <p>
    <button type="submit">Save assignments</button>
  </p>
</form>

<p><a href="index.php?page=back.services">Back to services</a></p>
