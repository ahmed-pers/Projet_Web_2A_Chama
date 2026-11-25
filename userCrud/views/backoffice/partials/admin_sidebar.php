<?php
// View: display only. Expects $adminPage ('reports' | 'categories'), $pendingCount; optional $filters['status']
$activeStatus = $filters['status'] ?? 'pending';
$onReports = $adminPage === 'reports';
?>
  <aside id="sidebar">

    <header>
      <a href="index.php?action=admin_reports">
        <img src="assets/imgs/logo.png" alt="CityFix logo" width="32" height="32">
        <span>CityFix admin</span>
      </a>
    </header>

    <p><a href="index.php?action=report_form">&larr; Back to front office</a></p>

    <h2>Moderation</h2>

    <nav>
      <ul>
        <li><a href="index.php?action=admin_reports&amp;status=pending"<?= $onReports && $activeStatus === 'pending' ? ' aria-current="page"' : '' ?>>Pending reports <span id="pending-count"><?= (int) $pendingCount ?></span></a></li>
        <li><a href="index.php?action=admin_reports&amp;status=in_progress"<?= $onReports && $activeStatus === 'in_progress' ? ' aria-current="page"' : '' ?>>In progress</a></li>
        <li><a href="index.php?action=admin_reports&amp;status=resolved"<?= $onReports && $activeStatus === 'resolved' ? ' aria-current="page"' : '' ?>>Resolved</a></li>
        <li><a href="index.php?action=admin_reports&amp;status="<?= $onReports && $activeStatus === '' ? ' aria-current="page"' : '' ?>>All statuses</a></li>
        <li><a href="index.php?action=admin_categories"<?= $adminPage === 'categories' ? ' aria-current="page"' : '' ?>>Categories</a></li>
      </ul>
    </nav>

    <footer id="admin-profile">
      <img id="admin-photo" src="assets/imgs/avatar.png" alt="Admin picture" width="40" height="40">
      <p>
        <strong id="admin-name"><?= e($_SESSION['username'] ?? '') ?></strong><br>
        <a href="index.php?action=logout"><small>Log out</small></a>
      </p>
    </footer>

  </aside>
