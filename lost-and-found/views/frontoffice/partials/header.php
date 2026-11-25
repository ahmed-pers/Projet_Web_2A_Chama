<?php
$currentPage = $currentPage ?? '';
$unread = $unread ?? 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lost &amp; Found - Front Office</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="layout">
<?php require __DIR__ . '/sidebar.php'; ?>
  <div class="content">
<?php if (!empty($notice)): ?>
    <p class="notice <?= e($notice['type']) ?>" role="status"><?= e($notice['text']) ?></p>
<?php endif; ?>
