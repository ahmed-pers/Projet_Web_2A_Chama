<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= e($title) ?></title>
</head>
<body>

  <h1>Smart Queue</h1>

  <h2>Menu</h2>
  <ul>
    <li><a href="index.php?page=front">Front Office - Dashboard</a></li>
    <li><a href="index.php?page=front.services">Front Office - Services</a></li>
    <li><a href="index.php?page=front.tickets">Front Office - My tickets</a></li>
    <li><a href="index.php?page=back.categories">Back Office - Categories</a></li>
    <li><a href="index.php?page=back.services">Back Office - Services</a></li>
    <li><a href="index.php?page=back.tickets">Back Office - Tickets</a></li>
  </ul>

  <?php if ($flash !== ''): ?>
    <p><strong><?= e($flash) ?></strong></p>
  <?php endif; ?>

  <?= $content ?>

  <script src="public/js/validation.js"></script>
</body>
</html>
