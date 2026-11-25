<?php // Expects $adminPage and $pendingCount. ?>
<aside class="sidebar">
  <header class="brand">
    <strong>LostFound admin</strong>
    <small>Back office</small>
  </header>

  <h2>Project pages</h2>
  <nav aria-label="Project pages">
    <ul class="service-links">
      <li><a href="../backoffice/FIXMYCITY.html">Fix My City</a></li>
      <li><a href="../backoffice/lost-and-found.php" aria-current="page">Lost and Found</a></li>
      <li><a href="../backoffice/smart-queue.html">Smart Queue</a></li>
      <li><a href="../backoffice/smart-sport.html">Smart Sport</a></li>
      <li><a href="../backoffice/Smart_Vet.html">Smart Vet</a></li>
      <li><a href="../backoffice/transport.php">Transport</a></li>
    </ul>
  </nav>

  <h2>Moderation</h2>
  <nav aria-label="Admin menu">
    <ul>
      <li><a href="index.php?action=admin_items&amp;status=pending"<?= $adminPage === 'items' ? ' aria-current="page"' : '' ?>>Pending items <span class="badge"><?= (int) $pendingCount ?></span></a></li>
      <li><a href="index.php?action=admin_items&amp;status=matched">Matched</a></li>
      <li><a href="index.php?action=admin_items&amp;status=returned">Returned</a></li>
      <li><a href="index.php?action=admin_items&amp;status=">All items</a></li>
      <li><a href="index.php?action=admin_categories"<?= $adminPage === 'categories' ? ' aria-current="page"' : '' ?>>Categories</a></li>
      <li><a href="index.php?action=admin_messages"<?= $adminPage === 'messages' ? ' aria-current="page"' : '' ?>>Messages</a></li>
    </ul>
  </nav>

  <p><a href="index.php?action=dashboard">&larr; Back to front office</a></p>

  <footer class="profile">
    <span class="avatar"><?= e(strtoupper(substr((string) ($_SESSION['username'] ?? 'A'), 0, 2))) ?></span>
    <p><strong><?= e($_SESSION['username'] ?? '') ?></strong><br><?= e($_SESSION['role'] ?? '') ?></p>
    <p><a href="index.php?action=logout">Log out</a></p>
  </footer>
</aside>
