<h2><?= isset($service['id']) ? 'Edit service' : 'Add a service' ?></h2>

<?php foreach ($errors as $error): ?>
  <p><strong><?= e($error) ?></strong></p>
<?php endforeach; ?>

<form method="post" action="index.php?page=<?= e($action) ?>" data-validate>
  <p>
    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="<?= e($service['name'] ?? '') ?>" data-required>
  </p>
  <p>
    <label for="description">Description</label>
    <input type="text" id="description" name="description" value="<?= e($service['description'] ?? '') ?>">
  </p>
  <p>
    <label for="average_time">Average time (minutes)</label>
    <input type="number" id="average_time" name="average_time" value="<?= e($service['average_time'] ?? '') ?>" data-required>
  </p>
  <p>
    <label for="status">Status</label>
    <select id="status" name="status">
      <option value="open" <?= ($service['status'] ?? 'open') === 'open' ? 'selected' : '' ?>>Open</option>
      <option value="closed" <?= ($service['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
    </select>
  </p>
  <p>
    <button type="submit">Save</button>
  </p>
</form>

<p><a href="index.php?page=back.services">Back to services</a></p>
