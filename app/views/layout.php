<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= $title ?></title>
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

  <h1>Smart Queue</h1>

  <ul class="nav">
    <li><a href="index.php?page=front">Front Office</a></li>
    <li><a href="index.php?page=front.services">Services</a></li>
    <li><a href="index.php?page=front.tickets">Reserve</a></li>
    <li><a href="index.php?page=back.categories">Categories</a></li>
    <li><a href="index.php?page=back.services">Services (Back)</a></li>
    <li><a href="index.php?page=back.tickets">Tickets (Back)</a></li>
  </ul>

  <?= $content ?>

  <script src="public/js/validation.js"></script>
</body>
</html>
