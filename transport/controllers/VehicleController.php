<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/VehicleModel.php';
require_once __DIR__ . '/../../fixmycity/controllers/AuthController.php';

class VehicleController
{
    private VehicleModel $vehicles;
    private const TYPES = ['Car', 'SUV', 'Van', 'Motorcycle', 'Scooter', 'Bicycle'];

    public function __construct()
    {
        $this->vehicles = new VehicleModel(Database::getConnexion());
    }

    public function frontOffice(): void
    {
        $vehicles = $this->vehicles->all(true);
        require __DIR__ . '/../views/frontoffice/vehicles.php';
    }

    public function backOffice(): void
    {
        AuthController::requireRole('admin');
        $errors = [];
        $editing = null;
        $notice = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->requireCsrf();
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
            if (($_POST['action'] ?? '') === 'delete') {
                if ($id > 0) $this->vehicles->delete($id);
                $this->redirect('deleted');
            }

            [$editing, $errors] = $this->validate($_POST);
            if (!$errors) {
                if ($id > 0) {
                    $this->vehicles->update($id, $editing);
                    $this->redirect('updated');
                }
                $this->vehicles->create($editing);
                $this->redirect('created');
            }
            $editing['id'] = $id;
        } elseif (isset($_GET['edit'])) {
            $editing = $this->vehicles->find((int) $_GET['edit']);
        }

        $vehicles = $this->vehicles->all();
        $notices = ['created' => 'Vehicle added.', 'updated' => 'Vehicle updated.', 'deleted' => 'Vehicle deleted.'];
        $notice = $notices[(string) ($_GET['notice'] ?? '')] ?? '';
        $form = $editing ?: ['id' => 0, 'name' => '', 'type' => '', 'location' => '', 'details' => '', 'price_per_day' => '', 'status' => 'available'];
        $types = self::TYPES;
        require __DIR__ . '/../views/backoffice/vehicles.php';
    }

    public static function escape($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['transport_csrf'])) $_SESSION['transport_csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['transport_csrf'];
    }

    private function requireCsrf(): void
    {
        $token = (string) ($_POST['csrf_token'] ?? '');
        if (empty($_SESSION['transport_csrf']) || !hash_equals($_SESSION['transport_csrf'], $token)) {
            http_response_code(403);
            exit('Invalid security token. Please go back and try again.');
        }
    }

    private function validate(array $input): array
    {
        $vehicle = [
            'name' => trim((string) ($input['name'] ?? '')),
            'type' => trim((string) ($input['type'] ?? '')),
            'location' => trim((string) ($input['location'] ?? '')),
            'details' => trim((string) ($input['details'] ?? '')),
            'price' => trim((string) ($input['price'] ?? '')),
            'status' => (string) ($input['status'] ?? 'available'),
        ];
        $errors = [];
        if ($vehicle['name'] === '' || mb_strlen($vehicle['name']) > 100) $errors[] = 'Enter a vehicle name (100 characters maximum).';
        if (!in_array($vehicle['type'], self::TYPES, true)) $errors[] = 'Choose a valid vehicle type.';
        if ($vehicle['location'] === '' || mb_strlen($vehicle['location']) > 100) $errors[] = 'Enter a location (100 characters maximum).';
        if (mb_strlen($vehicle['details']) > 255) $errors[] = 'Details must be 255 characters or fewer.';
        if (!is_numeric($vehicle['price']) || (float) $vehicle['price'] <= 0) $errors[] = 'Enter a price greater than zero.';
        if (!in_array($vehicle['status'], ['available', 'rented', 'maintenance'], true)) $errors[] = 'Choose a valid status.';
        return [$vehicle, $errors];
    }

    private function redirect(string $notice): void
    {
        header('Location: transport.php?notice=' . rawurlencode($notice));
        exit;
    }
}
