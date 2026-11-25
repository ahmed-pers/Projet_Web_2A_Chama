<?php require __DIR__ . '/partials/header.php'; ?>

<header class="page-header">
  <div>
    <h1>Report it. Find it. Claim it.</h1>
    <p>Today's lost &amp; found, <?= e(date('l, d F Y')) ?></p>
  </div>
  <a class="button" href="index.php?action=report_form">+ Report an item</a>
</header>

<section class="summary-grid" aria-label="Reported items summary">
  <article><span>Lost</span><strong><?= (int) ($counts['lost'] ?? 0) ?></strong></article>
  <article><span>Found</span><strong><?= (int) ($counts['found'] ?? 0) ?></strong></article>
  <article><span>Matched</span><strong><?= (int) ($counts['matched'] ?? 0) ?></strong></article>
  <article><span>Returned</span><strong><?= (int) ($counts['returned'] ?? 0) ?></strong></article>
</section>

<h2>Recently reported</h2>
<?php if (!$items): ?>
  <p class="empty">No items reported yet. <a href="index.php?action=report_form">Be the first to report one.</a></p>
<?php else: ?>
  <div class="card-grid">
    <?php foreach ($items as $item): ?>
      <article class="item-card">
        <div class="card-head">
          <span class="tag <?= e($item->getType()) ?>"><?= e(ucfirst($item->getType())) ?></span>
          <span class="tag <?= e($item->getStatus()) ?>"><?= e(ucfirst($item->getStatus())) ?></span>
        </div>
        <h3><a href="index.php?action=item_details&amp;id=<?= $item->getId() ?>"><?= e($item->getName()) ?></a></h3>
        <p class="muted"><?= e($item->getCategoryName()) ?> &middot; <?= e($item->getDateFound()) ?></p>
        <p><?= e($item->getDescription() ?: 'No description.') ?></p>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/right_panel.php'; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
