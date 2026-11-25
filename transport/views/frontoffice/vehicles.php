<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vehicle Rental</title>
  <style>.vehicle-table{width:100%}.vehicle-table th,.vehicle-table td{padding:6px}</style>
</head>
<body>
<table width="100%" cellpadding="16" cellspacing="0"><tr>
  <td width="230" valign="top" bgcolor="#f4f6f8"><aside><h2>Other services</h2><nav><ul>
    <li><a href="indexFIXMYCITY.html">Fix My City</a></li><li><a href="lost-and-found.html">Lost and Found</a></li><li><a href="smart-queue.html">Smart Queue</a></li><li><a href="smart-sport.html">Smart Sport</a></li><li><a href="Smart_Vet.html">Smart Vet</a></li><li><a href="transport.php" aria-current="page">Vehicle Rental</a></li>
  </ul></nav></aside></td>
  <td valign="top"><header><h1>Vehicle Rental</h1><p>Find a car, motorcycle, scooter or bicycle for your next journey.</p></header>
    <main><h2>Available vehicles</h2>
      <?php if (!$vehicles): ?><p>No vehicles are currently available.</p><?php else: ?>
      <table class="vehicle-table" border="1" cellpadding="6" cellspacing="0"><thead><tr><th>Vehicle</th><th>Type</th><th>Location</th><th>Details</th><th>Price per day</th></tr></thead><tbody>
        <?php foreach ($vehicles as $vehicle): ?><tr>
          <td><?= VehicleController::escape($vehicle['name']) ?></td><td><?= VehicleController::escape($vehicle['type']) ?></td><td><?= VehicleController::escape($vehicle['location']) ?></td><td><?= VehicleController::escape($vehicle['details'] ?: '-') ?></td><td><?= VehicleController::escape(number_format((float) $vehicle['price_per_day'], 2)) ?> TND</td>
        </tr><?php endforeach; ?>
      </tbody></table><?php endif; ?>
    </main>
  </td>
</tr></table>
</body>
</html>
