<?php require __DIR__ . '/partials/header.php'; ?>

<article class="detail-card">
  <div class="card-head">
    <span class="tag <?= e($item->getType()) ?>"><?= e(ucfirst($item->getType())) ?></span>
    <span class="tag <?= e($item->getStatus()) ?>"><?= e(ucfirst($item->getStatus())) ?></span>
  </div>

  <h1><?= e($item->getName()) ?></h1>

  <?php if ($item->getImageUrl()): ?>
    <img class="detail-photo" src="<?= e($item->getImageUrl()) ?>" alt="<?= e($item->getName()) ?>">
  <?php endif; ?>

  <dl class="detail-list">
    <dt>Category</dt><dd><?= e($item->getCategoryName()) ?></dd>
    <dt>Reported by</dt><dd><?= e($item->getOwnerName()) ?></dd>
    <dt>Date lost / found</dt><dd><?= e($item->getDateFound()) ?></dd>
    <dt>Description</dt><dd><?= nl2br(e($item->getDescription() ?: 'No description.')) ?></dd>
  </dl>

  <p class="actions">
    <a class="button" href="index.php?action=message_form&amp;id=<?= $item->getId() ?>">Send a message</a>
    <?php if ($item->getOwnerId() === (int) ($_SESSION['user_id'] ?? 0) && $item->getStatus() === 'pending'): ?>
      <a href="index.php?action=item_edit&amp;id=<?= $item->getId() ?>">Edit</a>
    <?php endif; ?>
    <a href="index.php?action=items">Back to list</a>
  </p>
</article>

<section class="panel">
  <h2>Messages about this item</h2>
  <?php if (!$messages): ?>
    <p class="empty">No messages yet.</p>
  <?php else: ?>
    <ul class="message-list">
      <?php foreach ($messages as $message): ?>
        <li>
          <strong><?= e($message->getSenderName()) ?></strong>
          <span class="muted">to <?= e($message->getReceiverName()) ?> &middot; <?= e($message->getDateSent()) ?></span>
          <p><?= nl2br(e($message->getText())) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
