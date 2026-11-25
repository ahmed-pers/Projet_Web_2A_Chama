<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vehicle Rental - Back Office</title>
  <style>.vehicle-table{width:100%}.vehicle-table th,.vehicle-table td{padding:6px}</style>
</head>
<body>
<table width="100%" cellpadding="16" cellspacing="0"><tr>
  <td width="230" valign="top" bgcolor="#f4f6f8"><aside><h2>Other services</h2><nav><ul>
    <li><a href="FIXMYCITY.html">Fix My City</a></li><li><a href="lost-and-found.html">Lost and Found</a></li><li><a href="smart-queue.html">Smart Queue</a></li><li><a href="smart-sport.html">Smart Sport</a></li><li><a href="Smart_Vet.html">Smart Vet</a></li><li><a href="transport.php" aria-current="page">Vehicle Rental</a></li>
  </ul></nav></aside></td>
  <td valign="top"><header><h1>Vehicle Rental - Back Office</h1><p>Manage vehicle listings, rental prices and availability.</p></header>
    <main>
      <?php if ($notice): ?><p role="status"><?= VehicleController::escape($notice) ?></p><?php endif; ?>
      <?php foreach ($errors as $error): ?><p role="alert"><?= VehicleController::escape($error) ?></p><?php endforeach; ?>
      <section id="vehicles"><h2>Vehicle listings</h2>
        <table class="vehicle-table" border="1" cellpadding="6" cellspacing="0"><thead><tr><th>Vehicle</th><th>Type</th><th>Location</th><th>Price / day</th><th>Status</th><th>Actions</th></tr></thead><tbody>
          <?php foreach ($vehicles as $vehicle): ?><tr>
            <td><?= VehicleController::escape($vehicle['name']) ?></td><td><?= VehicleController::escape($vehicle['type']) ?></td><td><?= VehicleController::escape($vehicle['location']) ?></td><td><?= VehicleController::escape(number_format((float) $vehicle['price_per_day'], 2)) ?> TND</td><td><?= VehicleController::escape(ucfirst($vehicle['status'])) ?></td>
            <td><a href="?edit=<?= (int) $vehicle['id'] ?>#vehicle-form">Edit</a>
              <form action="transport.php" method="post" style="display:inline"><input type="hidden" name="csrf_token" value="<?= VehicleController::escape(VehicleController::csrfToken()) ?>"><input type="hidden" name="id" value="<?= (int) $vehicle['id'] ?>"><input type="hidden" name="action" value="delete"><button type="submit">Delete</button></form></td>
          </tr><?php endforeach; ?>
          <?php if (!$vehicles): ?><tr><td colspan="6">No vehicles have been added.</td></tr><?php endif; ?>
        </tbody></table>
      </section>
      <section id="vehicle-form"><h2><?= $form['id'] ? 'Edit vehicle' : 'Add a vehicle' ?></h2>
        <form action="transport.php" method="post">
          <input type="hidden" name="csrf_token" value="<?= VehicleController::escape(VehicleController::csrfToken()) ?>"><input type="hidden" name="id" value="<?= (int) $form['id'] ?>">
          Make and model: <input type="text" name="name" maxlength="100" value="<?= VehicleController::escape($form['name']) ?>" required><br>
          Type: <select name="type" required><option value="">Choose a type</option><?php foreach ($types as $type): ?><option value="<?= VehicleController::escape($type) ?>"<?= $form['type'] === $type ? ' selected' : '' ?>><?= VehicleController::escape($type) ?></option><?php endforeach; ?></select><br>
          Location: <input type="text" name="location" maxlength="100" value="<?= VehicleController::escape($form['location']) ?>" required><br>
          Details: <input type="text" name="details" maxlength="255" value="<?= VehicleController::escape($form['details']) ?>"><br>
          Price per day (TND): <input type="number" name="price" min="1" step="0.01" value="<?= VehicleController::escape($form['price_per_day'] ?? $form['price'] ?? '') ?>" required><br>
          Status: <select name="status"><option value="available"<?= $form['status'] === 'available' ? ' selected' : '' ?>>Available</option><option value="rented"<?= $form['status'] === 'rented' ? ' selected' : '' ?>>Rented</option><option value="maintenance"<?= $form['status'] === 'maintenance' ? ' selected' : '' ?>>Maintenance</option></select><br>
          <button type="submit">Save vehicle</button>
        </form>
      </section>
    </main>
  </td>
</tr></table>
</body>
</html>
