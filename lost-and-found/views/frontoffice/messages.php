<?php require __DIR__ . '/partials/header.php'; ?>

<header class="page-header">
  <div>
    <h1>Messages</h1>
    <p>Messages you sent or received about lost &amp; found items.</p>
  </div>
</header>

<?php if (!$messages): ?>
  <p class="empty">No messages yet.</p>
<?php else: ?>
  <section class="panel">
    <ul class="message-list">
      <?php foreach ($messages as $message): ?>
        <li>
          <div class="card-head">
            <strong><?= e($message->getSenderName()) ?> &rarr; <?= e($message->getReceiverName()) ?></strong>
            <?= $message->isRead() ? '<span class="tag returned">Read</span>' : '<span class="tag pending">Unread</span>' ?>
          </div>
          <p class="muted">
            <a href="index.php?action=item_details&amp;id=<?= $message->getItemId() ?>"><?= e($message->getItemName()) ?></a>
            &middot; <?= e($message->getDateSent()) ?>
          </p>
          <p><?= nl2br(e($message->getText())) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
