<?php // View: display only. Sidebar: "Report a problem" + "How to use". Needs $currentPage ?>
  <aside id="sidebar">
    <header>
      <a href="index.php?action=report_form">
        <img src="assets/imgs/logo.png" alt="FIXMYCITY logo" width="40" height="40">
        <span>FIXMYCITY</span>
      </a>
    </header>

    <nav>
      <ul>
        <li><a href="index.php?action=report_form"<?= ($currentPage ?? '') === 'report_form' ? ' aria-current="page"' : '' ?>>Report a problem</a></li>
        <li><a href="index.php?action=info">How to use</a></li>
        <li><a href="index.php?action=logout">Log out</a></li>
      </ul>
    </nav>
  </aside>
