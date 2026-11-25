<?php require __DIR__ . '/partials/header.php'; ?>

<section class="panel form-panel">
  <h1>Send a message</h1>
  <p class="muted">About: <strong><?= e($item->getName()) ?></strong></p>

  <?php foreach ($errors as $error): ?>
    <p class="notice error" role="alert"><?= e($error) ?></p>
  <?php endforeach; ?>

  <form method="post" class="form" action="index.php?action=message_create">
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
    <input type="hidden" name="id_item" value="<?= (int) $form['id_item'] ?>">

    <label>Receiver *
      <select name="receiver_id" required>
        <option value="">Choose a user</option>
        <?php foreach ($users as $user): ?>
          <option value="<?= (int) $user['id'] ?>"<?= (string) ($form['receiver_id'] ?? '') === (string) $user['id'] ? ' selected' : '' ?>>
            <?= e($user['username']) ?> (<?= e($user['role']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Message *
      <textarea name="message_text" rows="5" maxlength="1000" required><?= e($form['message_text'] ?? '') ?></textarea>
    </label>

    <p class="actions">
      <button class="button" type="submit">Send message</button>
      <a href="index.php?action=item_details&amp;id=<?= $item->getId() ?>">Cancel</a>
    </p>
  </form>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
