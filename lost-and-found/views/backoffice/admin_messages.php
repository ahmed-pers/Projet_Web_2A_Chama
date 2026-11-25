<?php require __DIR__ . '/partials/header.php'; ?>

<header class="page-header">
  <div>
    <h1>Messages</h1>
    <p>All messages sent between users about reported items.</p>
  </div>
</header>

<div class="table-wrap">
  <table class="data-table">
    <thead><tr><th>Item</th><th>From</th><th>To</th><th>Message</th><th>Date</th><th>Read</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($messages as $message): ?>
        <tr>
          <td><a href="index.php?action=item_details&amp;id=<?= $message->getItemId() ?>"><?= e($message->getItemName()) ?></a></td>
          <td><?= e($message->getSenderName()) ?></td>
          <td><?= e($message->getReceiverName()) ?></td>
          <td><?= nl2br(e($message->getText())) ?></td>
          <td><?= e($message->getDateSent()) ?></td>
          <td><?= $message->isRead() ? 'Yes' : 'No' ?></td>
          <td>
            <?php if (!$message->isRead()): ?>
              <form method="post" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="id_message" value="<?= (int) $message->getId() ?>">
                <button type="submit" formaction="index.php?action=admin_message_read">Mark read</button>
              </form>
            <?php else: ?><span class="muted">Done</span><?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$messages): ?><tr><td colspan="7">No messages yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
