<?php // Front office navigation. Expects $currentPage and $unread. ?>
<aside class="sidebar">
  <header class="brand">
    <strong>SmartLostFound</strong>
    <small>Front office</small>
  </header>

  <h2>Services</h2>
  <nav aria-label="Project pages">
    <ul class="service-links">
      <li><a href="../frontoffice/indexFIXMYCITY.html">Fix My City</a></li>
      <li><a href="../frontoffice/lost-and-found.php" aria-current="page">Lost and Found</a></li>
      <li><a href="../frontoffice/smart-queue.html">Smart Queue</a></li>
      <li><a href="../frontoffice/smart-sport.html">Smart Sport</a></li>
      <li><a href="../frontoffice/Smart_Vet.html">Smart Vet</a></li>
      <li><a href="../frontoffice/transport.php">Transport</a></li>
    </ul>
  </nav>

  <h2>Main menu</h2>
  <nav aria-label="Lost and Found menu">
    <ul>
      <li><a href="index.php?action=dashboard"<?= $currentPage === 'dashboard' ? ' aria-current="page"' : '' ?>>Dashboard</a></li>
      <li><a href="index.php?action=report_form"<?= $currentPage === 'report_form' ? ' aria-current="page"' : '' ?>>Report an item</a></li>
      <li><a href="index.php?action=items"<?= $currentPage === 'items' ? ' aria-current="page"' : '' ?>>Lost &amp; found items</a></li>
      <li><a href="index.php?action=my_items"<?= $currentPage === 'my_items' ? ' aria-current="page"' : '' ?>>My items</a></li>
      <li><a href="index.php?action=messages"<?= $currentPage === 'messages' ? ' aria-current="page"' : '' ?>>Messages <?= (int) $unread > 0 ? '<span class="badge">' . (int) $unread . '</span>' : '' ?></a></li>
      <li><a href="index.php?action=admin_items">Back office</a></li>
    </ul>
  </nav>

  <footer class="profile">
    <span class="avatar"><?= e(strtoupper(substr((string) ($_SESSION['username'] ?? 'U'), 0, 2))) ?></span>
    <p><strong><?= e($_SESSION['username'] ?? '') ?></strong><br><?= e($_SESSION['role'] ?? '') ?></p>
    <p><a href="index.php?action=logout">Log out</a></p>
  </footer>
</aside>
