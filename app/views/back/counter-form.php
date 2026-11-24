<h2><?= isset($counter['id']) ? 'Edit counter' : 'Add a counter' ?></h2>

<?php foreach ($errors as $error): ?>
  <p><strong><?= e($error) ?></strong></p>
<?php endforeach; ?>

<form method="post" action="index.php?page=<?= e($action) ?>" data-validate>
  <p>
    <label for="number">Number</label>
    <input type="text" id="number" name="number" value="<?= e($counter['number'] ?? '') ?>" data-required>
  </p>
  <p>
    <label for="location">Location</label>
    <input type="text" id="location" name="location" value="<?= e($counter['location'] ?? '') ?>">
  </p>
  <p>
    <label for="status">Status</label>
    <select id="status" name="status">
      <option value="open" <?= ($counter['status'] ?? 'open') === 'open' ? 'selected' : '' ?>>Open</option>
      <option value="closed" <?= ($counter['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
    </select>
  </p>
  <p>
    <button type="submit">Save</button>
  </p>
</form>

<p><a href="index.php?page=back.counters">Back to counters</a></p>
