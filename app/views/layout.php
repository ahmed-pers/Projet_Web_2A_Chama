<?php
$links = [
    'front'           => 'Front Office',
    'front.services'  => 'Services',
    'front.tickets'   => 'Reserve',
    'back.categories' => 'Categories',
    'back.services'   => 'Services (Back)',
    'back.tickets'    => 'Tickets (Back)',
];
$current = $_GET['page'] ?? 'front';
$active = $current;
if (str_starts_with($active, 'back.categorie')) {
    $active = 'back.categories';
}
if (str_starts_with($active, 'back.service')) {
    $active = 'back.services';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?></title>
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

  <header class="topbar">
    <div class="brand">
      <span class="brand-mark">SQ</span>
      <span>
        <strong>Smart Queue</strong>
        <small>Queue management</small>
      </span>
    </div>

    <nav class="nav" aria-label="Main navigation">
      <?php foreach ($links as $page => $label): ?>
        <a class="nav-link <?= $page === $active ? 'is-active' : '' ?>" href="index.php?page=<?= $page ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </nav>
  </header>

  <main class="main">
    <?= $content ?>
  </main>

  <footer class="footer">Smart Queue · MVC + PDO</footer>

  <script src="public/js/validation.js"></script>
</body>
</html>
