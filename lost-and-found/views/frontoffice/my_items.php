<?php require __DIR__ . '/partials/header.php'; ?>

<header class="page-header">
  <div>
    <h1>My Items</h1>
    <p>Reports you created. You can edit or delete them while they are still pending.</p>
  </div>
  <a class="button" href="index.php?action=report_form">+ Report an item</a>
</header>

<?php if (!$items): ?>
  <p class="empty">You have not reported any items yet.</p>
<?php else: ?>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>Item</th><th>Type</th><th>Category</th><th>Date</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><a href="index.php?action=item_details&amp;id=<?= $item->getId() ?>"><?= e($item->getName()) ?></a></td>
            <td><?= e(ucfirst($item->getType())) ?></td>
            <td><?= e($item->getCategoryName()) ?></td>
            <td><?= e($item->getDateFound()) ?></td>
            <td><span class="tag <?= e($item->getStatus()) ?>"><?= e(ucfirst($item->getStatus())) ?></span></td>
            <td>
              <?php if ($item->getStatus() === 'pending'): ?>
                <a href="index.php?action=item_edit&amp;id=<?= $item->getId() ?>">Edit</a>
                <form method="post" class="inline-form" action="index.php?action=item_delete" data-confirm="Delete this item?">
                  <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                  <input type="hidden" name="id_item" value="<?= (int) $item->getId() ?>">
                  <button class="link-danger" type="submit">Delete</button>
                </form>
              <?php else: ?>
                <span class="muted">Locked</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
