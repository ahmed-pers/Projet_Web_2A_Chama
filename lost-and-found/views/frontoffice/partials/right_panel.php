<?php // Expects $counts and $unread (both optional). ?>
<?php $counts = $counts ?? []; $unread = $unread ?? 0; ?>
<aside class="right-panel">
  <section>
    <h3>Summary</h3>
    <ul class="stat-list">
      <li><strong><?= (int) ($counts['lost'] ?? 0) ?></strong> Lost</li>
      <li><strong><?= (int) ($counts['found'] ?? 0) ?></strong> Found</li>
      <li><strong><?= (int) ($counts['matched'] ?? 0) ?></strong> Matched</li>
      <li><strong><?= (int) ($counts['returned'] ?? 0) ?></strong> Returned</li>
    </ul>
  </section>

  <section>
    <h3>Announcements</h3>
    <article>
      <h4>Claim desk</h4>
      <p>Collect matched items at Counter 2, 09:00&ndash;17:00.</p>
    </article>
    <article>
      <h4>Photo reminder</h4>
      <p>Add a clear photo when you report an item.</p>
    </article>
  </section>

  <section>
    <h3>Messages</h3>
    <p>You have <strong><?= (int) $unread ?></strong> unread message(s).</p>
    <p><a href="index.php?action=messages">Open inbox</a></p>
  </section>
</aside>
