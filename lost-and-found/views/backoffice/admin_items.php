<?php require __DIR__ . '/partials/header.php'; ?>

<header class="page-header">
  <div>
    <h1>Items</h1>
    <p>Manage every lost &amp; found item, its status and its photo.</p>
  </div>
  <a class="button" href="index.php?action=report_form">+ Add item</a>
</header>

<section class="filters">
  <form method="get" class="filter-form">
    <input type="hidden" name="action" value="admin_items">
    <label>Search <input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Item, category, owner"></label>
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

<div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr><th>Item</th><th>Type</th><th>Category</th><th>Owner</th><th>Date</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td>
            <a href="index.php?action=item_details&amp;id=<?= $item->getId() ?>"><?= e($item->getName()) ?></a>
            <?php if ($item->getImageUrl()): ?><br><img class="thumb" src="<?= e($item->getImageUrl()) ?>" alt=""><?php endif; ?>
          </td>
          <td><?= e(ucfirst($item->getType())) ?></td>
          <td><?= e($item->getCategoryName()) ?></td>
          <td><?= e($item->getOwnerName()) ?></td>
          <td><?= e($item->getDateFound()) ?></td>
          <td><span class="tag <?= e($item->getStatus()) ?>"><?= e(ucfirst($item->getStatus())) ?></span></td>
          <td>
            <?php if ($item->getStatus() === 'pending'): ?>
              <form method="post" class="inline-form" action="index.php?action=admin_item_status">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="id_item" value="<?= (int) $item->getId() ?>">
                <input type="hidden" name="to" value="matched">
                <button type="submit">Mark matched</button>
              </form>
            <?php elseif ($item->getStatus() === 'matched'): ?>
              <form method="post" class="inline-form" action="index.php?action=admin_item_status">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="id_item" value="<?= (int) $item->getId() ?>">
                <input type="hidden" name="to" value="returned">
                <button type="submit">Mark returned</button>
              </form>
            <?php else: ?>
              <span class="muted">Done</span>
            <?php endif; ?>

            <form method="post" class="inline-form" action="index.php?action=admin_item_delete" data-confirm="Delete this item and its messages?">
              <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
              <input type="hidden" name="id_item" value="<?= (int) $item->getId() ?>">
              <button class="link-danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$items): ?><tr><td colspan="7">No items match your filters.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
