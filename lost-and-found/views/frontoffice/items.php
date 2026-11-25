<?php require __DIR__ . '/partials/header.php'; ?>

<header class="page-header">
  <div>
    <h1>Lost &amp; Found Items</h1>
    <p>Browse every reported item and contact the sender for details.</p>
  </div>
  <a class="button" href="index.php?action=report_form">+ Report an item</a>
</header>

<section class="filters">
  <form method="get" class="filter-form">
    <input type="hidden" name="action" value="items">
    <label>Search <input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Item, description, category"></label>
    <label>Type
      <select name="type">
        <option value="">All</option>
        <?php foreach (['lost' => 'Lost', 'found' => 'Found'] as $value => $label): ?>
          <option value="<?= e($value) ?>"<?= $filters['type'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Status
      <select name="status">
        <option value="">All</option>
        <?php foreach (['pending' => 'Pending', 'matched' => 'Matched', 'returned' => 'Returned'] as $value => $label): ?>
          <option value="<?= e($value) ?>"<?= $filters['status'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <button type="submit">Filter</button>
  </form>
</section>

<?php if (!$items): ?>
  <p class="empty">No items match your filters.</p>
<?php else: ?>
  <div class="card-grid">
    <?php foreach ($items as $item): ?>
      <article class="item-card">
        <div class="card-head">
          <span class="tag <?= e($item->getType()) ?>"><?= e(ucfirst($item->getType())) ?></span>
          <span class="tag <?= e($item->getStatus()) ?>"><?= e(ucfirst($item->getStatus())) ?></span>
        </div>
        <?php if ($item->getImageUrl()): ?>
          <img class="item-photo" src="<?= e($item->getImageUrl()) ?>" alt="<?= e($item->getName()) ?>">
        <?php endif; ?>
        <h3><a href="index.php?action=item_details&amp;id=<?= $item->getId() ?>"><?= e($item->getName()) ?></a></h3>
        <p class="muted"><?= e($item->getCategoryName()) ?> &middot; <?= e($item->getDateFound()) ?> &middot; reported by <?= e($item->getOwnerName()) ?></p>
        <p><?= e($item->getDescription() ?: 'No description.') ?></p>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/right_panel.php'; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
