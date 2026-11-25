<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ItemModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/MessageModel.php';
require_once __DIR__ . '/../../fixmycity/controllers/AuthController.php';

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * All logic for the Lost & Found module: validation, uploads, PDO queries and routing helpers.
 * Views only display the data they receive.
 */
class ItemController
{
    private const TYPES = ['lost', 'found'];
    private const STATUSES = ['pending', 'matched', 'returned'];
    private const WORKFLOW = ['matched' => 'pending', 'returned' => 'matched'];
    private const MAX_PHOTO_SIZE = 5242880; // 5 MB
    private const ALLOWED_MIME = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    private const NOTICES = [
        'item_created'  => ['success', 'Item reported successfully.'],
        'item_updated'  => ['success', 'Item updated.'],
        'item_deleted'  => ['success', 'Item deleted.'],
        'item_missing'  => ['error',   'This item does not exist or cannot be changed anymore.'],
        'message_sent'  => ['success', 'Message sent to the receiver.'],
        'status_ok'     => ['success', 'Item status updated.'],
        'status_bad'    => ['error',   'This status change is not allowed (pending -> matched -> returned only).'],
        'cat_created'   => ['success', 'Category created.'],
        'cat_updated'   => ['success', 'Category updated.'],
        'cat_deleted'   => ['success', 'Category deleted.'],
        'cat_invalid'   => ['error',   'A category name (100 characters max) is required.'],
        'cat_exists'    => ['error',   'A category with this name already exists.'],
        'cat_in_use'    => ['error',   'This category is used by items and cannot be deleted.'],
        'message_read'  => ['success', 'Message marked as read.'],
    ];

    private PDO $db;
    private AuthController $auth;
    private string $rootDir;

    public function __construct()
    {
        $this->db = Database::getConnexion();
        $this->auth = new AuthController();
        $this->rootDir = dirname(__DIR__) . '/';
    }

    public static function escape($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /* =====================================================
       FRONT OFFICE
       ===================================================== */

    public function dashboard(): void
    {
        AuthController::requireAuth();

        $this->render('frontoffice/dashboard', [
            'currentPage' => 'dashboard',
            'counts'      => $this->countByStatus(),
            'items'       => $this->fetchItems('1 = 1', [], 6),
            'unread'      => $this->countUnreadMessages(),
            'notice'      => $this->getNotice($_GET['notice'] ?? ''),
        ]);
    }

    public function showReportForm(array $errors = [], array $old = []): void
    {
        AuthController::requireAuth();

        $this->render('frontoffice/item_form', [
            'currentPage' => 'report_form',
            'categories'  => $this->fetchCategories(),
            'errors'      => $errors,
            'form'        => $old + $this->emptyItemForm(),
            'item'        => null,
            'notice'      => $this->getNotice($_GET['notice'] ?? ''),
        ]);
    }

    public function createItem(): void
    {
        AuthController::requireAuth();
        $this->requirePost();
        $this->requireCsrfOrDie();

        [$errors, $clean] = $this->validateItem($_POST, $_FILES, true);
        if ($errors) {
            http_response_code(422);
            $this->showReportForm($errors, $_POST);
            return;
        }

        $photo = null;
        if ($this->hasUploadedPhoto($_FILES['image'] ?? null)) {
            $photo = $this->savePhoto($_FILES['image']);
            if ($photo === null) {
                http_response_code(500);
                $this->showReportForm(['image' => 'The photo could not be saved. Please try again.'], $_POST);
                return;
            }
        }

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO items (id_category, owner_id, item_name, description, type, status, date_found, image_url)
                 VALUES (:category, :owner, :name, :description, :type, :status, :date, :image)'
            );
            $stmt->execute([
                'category'    => $clean['category'],
                'owner'       => $this->currentUserId(),
                'name'        => $clean['name'],
                'description' => $clean['description'],
                'type'        => $clean['type'],
                'status'      => 'pending',
                'date'        => $clean['date'],
                'image'       => $photo,
            ]);
        } catch (PDOException $ex) {
            if ($photo !== '') $this->deletePhotoFile($photo);
            http_response_code(500);
            $this->showReportForm(['form' => 'The item could not be saved. Please try again.'], $_POST);
            return;
        }

        $this->redirect('items', 'item_created');
    }

    public function showItems(): void
    {
        AuthController::requireAuth();

        $filters = [
            'q'      => trim((string) ($_GET['q'] ?? '')),
            'type'   => (string) ($_GET['type'] ?? ''),
            'status' => (string) ($_GET['status'] ?? ''),
        ];

        $where  = ['1 = 1'];
        $params = [];

        if ($filters['q'] !== '') {
            $where[] = '(i.item_name LIKE :q OR i.description LIKE :qd OR c.name LIKE :qc)';
            $like = '%' . $filters['q'] . '%';
            $params['q'] = $like;
            $params['qd'] = $like;
            $params['qc'] = $like;
        }
        if (in_array($filters['type'], self::TYPES, true)) {
            $where[] = 'i.type = :type';
            $params['type'] = $filters['type'];
        }
        if (in_array($filters['status'], self::STATUSES, true)) {
            $where[] = 'i.status = :status';
            $params['status'] = $filters['status'];
        }

        $this->render('frontoffice/items', [
            'currentPage' => 'items',
            'items'       => $this->fetchItems(implode(' AND ', $where), $params),
            'filters'     => $filters,
            'notice'      => $this->getNotice($_GET['notice'] ?? ''),
        ]);
    }

    public function showItemDetails(): void
    {
        AuthController::requireAuth();

        $item = $this->findItem((int) ($_GET['id'] ?? 0));
        if ($item === null) {
            $this->redirect('items', 'item_missing');
            return;
        }

        $this->render('frontoffice/item_details', [
            'currentPage' => 'items',
            'item'        => $item,
            'messages'    => $this->fetchMessages('m.id_item = :id', ['id' => $item->getId()]),
            'notice'      => $this->getNotice($_GET['notice'] ?? ''),
        ]);
    }

    public function showMyItems(): void
    {
        AuthController::requireAuth();

        $this->render('frontoffice/my_items', [
            'currentPage' => 'my_items',
            'items'       => $this->fetchItems('i.owner_id = :uid', ['uid' => $this->currentUserId()]),
            'notice'      => $this->getNotice($_GET['notice'] ?? ''),
        ]);
    }

    public function showEditItem(): void
    {
        AuthController::requireAuth();

        $item = $this->findEditableItem((int) ($_GET['id'] ?? 0));
        if ($item === null) {
            $this->redirect('my_items', 'item_missing');
            return;
        }

        $this->render('frontoffice/item_form', [
            'currentPage' => 'my_items',
            'categories'  => $this->fetchCategories(),
            'errors'      => [],
            'form'        => $this->itemToForm($item),
            'item'        => $item,
            'notice'      => null,
        ]);
    }

    public function updateItem(): void
    {
        AuthController::requireAuth();
        $this->requirePost();
        $this->requireCsrfOrDie();

        $item = $this->findEditableItem((int) ($_POST['id_item'] ?? 0));
        if ($item === null) {
            $this->redirect('my_items', 'item_missing');
            return;
        }

        [$errors, $clean] = $this->validateItem($_POST, $_FILES, false);
        if ($errors) {
            http_response_code(422);
            $this->render('frontoffice/item_form', [
                'currentPage' => 'my_items',
                'categories'  => $this->fetchCategories(),
                'errors'      => $errors,
                'form'        => $_POST + ['id_item' => $item->getId()],
                'item'        => $item,
                'notice'      => null,
            ]);
            return;
        }

        $photo = $item->getImageUrl();
        if ($this->hasUploadedPhoto($_FILES['image'] ?? null)) {
            $newPhoto = $this->savePhoto($_FILES['image']);
            if ($newPhoto === null) {
                http_response_code(500);
                $this->render('frontoffice/item_form', [
                    'currentPage' => 'my_items',
                    'categories'  => $this->fetchCategories(),
                    'errors'      => ['image' => 'The photo could not be saved. Please try again.'],
                    'form'        => $_POST + ['id_item' => $item->getId()],
                    'item'        => $item,
                    'notice'      => null,
                ]);
                return;
            }
            $photo = $newPhoto;
        }

        $stmt = $this->db->prepare(
            'UPDATE items SET id_category = :category, item_name = :name, description = :description,
                    type = :type, date_found = :date, image_url = :image
             WHERE id_item = :id AND owner_id = :uid AND status = "pending"'
        );
        $stmt->execute([
            'category'    => $clean['category'],
            'name'        => $clean['name'],
            'description' => $clean['description'],
            'type'        => $clean['type'],
            'date'        => $clean['date'],
            'image'       => $photo,
            'id'          => $item->getId(),
            'uid'         => $this->currentUserId(),
        ]);

        if ($photo !== $item->getImageUrl()) $this->deletePhotoFile($item->getImageUrl());
        $this->redirect('item_details', 'item_updated', ['id' => $item->getId()]);
    }

    public function deleteItem(): void
    {
        AuthController::requireAuth();
        $this->requirePost();
        $this->requireCsrfOrDie();

        $item = $this->findEditableItem((int) ($_POST['id_item'] ?? 0));
        if ($item !== null) {
            $stmt = $this->db->prepare('DELETE FROM items WHERE id_item = :id AND owner_id = :uid AND status = "pending"');
            $stmt->execute(['id' => $item->getId(), 'uid' => $this->currentUserId()]);
            if ($stmt->rowCount() > 0) $this->deletePhotoFile($item->getImageUrl());
        }

        $this->redirect('my_items', 'item_deleted');
    }

    public function showMessageForm(): void
    {
        AuthController::requireAuth();

        $item = $this->findItem((int) ($_GET['id'] ?? 0));
        if ($item === null) {
            $this->redirect('items', 'item_missing');
            return;
        }

        $this->render('frontoffice/message_form', [
            'currentPage' => 'items',
            'item'        => $item,
            'users'       => $this->fetchUsers((int) $_SESSION['user_id']),
            'errors'      => [],
            'form'        => ['id_item' => $item->getId(), 'receiver_id' => '', 'message_text' => ''],
            'notice'      => null,
        ]);
    }

    public function createMessage(): void
    {
        AuthController::requireAuth();
        $this->requirePost();
        $this->requireCsrfOrDie();

        $itemId = (int) ($_POST['id_item'] ?? 0);
        $item = $this->findItem($itemId);
        if ($item === null) {
            $this->redirect('items', 'item_missing');
            return;
        }

        $receiverId = (string) ($_POST['receiver_id'] ?? '');
        $text = trim((string) ($_POST['message_text'] ?? ''));

        $errors = [];
        if (!ctype_digit($receiverId) || !$this->userExists((int) $receiverId) || (int) $receiverId === $this->currentUserId()) {
            $errors['receiver'] = 'Please choose another user as the receiver.';
        }
        if ($text === '' || mb_strlen($text) > 1000) {
            $errors['message_text'] = 'Write a message of 1000 characters or fewer.';
        }

        if ($errors) {
            http_response_code(422);
            $this->render('frontoffice/message_form', [
                'currentPage' => 'items',
                'item'        => $item,
                'users'       => $this->fetchUsers($this->currentUserId()),
                'errors'      => $errors,
                'form'        => $_POST,
                'notice'      => null,
            ]);
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO messages (id_item, sender_id, receiver_id, message_text)
             VALUES (:item, :sender, :receiver, :text)'
        );
        $stmt->execute([
            'item'     => $itemId,
            'sender'   => $this->currentUserId(),
            'receiver' => (int) $receiverId,
            'text'     => $text,
        ]);

        $this->redirect('item_details', 'message_sent', ['id' => $itemId]);
    }

    public function showMessages(): void
    {
        AuthController::requireAuth();

        $this->render('frontoffice/messages', [
            'currentPage' => 'messages',
            'messages'    => $this->fetchMessages('(m.sender_id = :sender OR m.receiver_id = :receiver)', [
                'sender'   => $this->currentUserId(),
                'receiver' => $this->currentUserId(),
            ]),
            'notice'      => $this->getNotice($_GET['notice'] ?? ''),
        ]);
    }

    /* =====================================================
       BACK OFFICE
       ===================================================== */

    public function showAdminItems(): void
    {
        AuthController::requireRole('admin');

        $filters = [
            'q'      => trim((string) ($_GET['q'] ?? '')),
            'type'   => (string) ($_GET['type'] ?? ''),
            'status' => array_key_exists('status', $_GET) ? (string) $_GET['status'] : 'pending',
        ];
        if (!in_array($filters['status'], self::STATUSES, true)) {
            $filters['status'] = '';
        }

        $where  = ['1 = 1'];
        $params = [];
        if ($filters['q'] !== '') {
            $where[] = '(i.item_name LIKE :q OR i.description LIKE :qd OR c.name LIKE :qc OR u.username LIKE :qu)';
            $like = '%' . $filters['q'] . '%';
            foreach (['q', 'qd', 'qc', 'qu'] as $key) {
                $params[$key] = $like;
            }
        }
        if (in_array($filters['type'], self::TYPES, true)) {
            $where[] = 'i.type = :type';
            $params['type'] = $filters['type'];
        }
        if ($filters['status'] !== '') {
            $where[] = 'i.status = :status';
            $params['status'] = $filters['status'];
        }

        $this->render('backoffice/admin_items', [
            'adminPage'    => 'items',
            'items'        => $this->fetchItems(implode(' AND ', $where), $params),
            'filters'      => $filters,
            'pendingCount' => $this->countByStatus()['pending'],
            'notice'       => $this->getNotice($_GET['notice'] ?? ''),
        ]);
    }

    public function changeItemStatus(): void
    {
        AuthController::requireRole('admin');
        $this->requirePost();
        $this->requireCsrfOrDie();

        $id = (int) ($_POST['id_item'] ?? 0);
        $to = (string) ($_POST['to'] ?? '');

        if (!isset(self::WORKFLOW[$to])) {
            $this->redirect('admin_items', 'status_bad', ['status' => '']);
            return;
        }

        $stmt = $this->db->prepare('UPDATE items SET status = :to WHERE id_item = :id AND status = :from');
        $stmt->execute(['to' => $to, 'id' => $id, 'from' => self::WORKFLOW[$to]]);

        $this->redirect('admin_items', $stmt->rowCount() > 0 ? 'status_ok' : 'status_bad', ['status' => '']);
    }

    public function deleteAdminItem(): void
    {
        AuthController::requireRole('admin');
        $this->requirePost();
        $this->requireCsrfOrDie();

        $id = (int) ($_POST['id_item'] ?? 0);
        $stmt = $this->db->prepare('SELECT image_url FROM items WHERE id_item = :id');
        $stmt->execute(['id' => $id]);
        $photo = $stmt->fetchColumn();

        $stmt = $this->db->prepare('DELETE FROM items WHERE id_item = :id');
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() > 0 && $photo) $this->deletePhotoFile((string) $photo);

        $this->redirect('admin_items', 'item_deleted', ['status' => '']);
    }

    public function showAdminCategories(): void
    {
        AuthController::requireRole('admin');

        $this->render('backoffice/admin_categories', [
            'adminPage'    => 'categories',
            'categories'   => $this->fetchCategories(),
            'pendingCount' => $this->countByStatus()['pending'],
            'notice'       => $this->getNotice($_GET['notice'] ?? ''),
        ]);
    }

    public function saveCategory(): void
    {
        AuthController::requireRole('admin');
        $this->requirePost();
        $this->requireCsrfOrDie();

        $id = (int) ($_POST['id_category'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));

        if ($name === '' || mb_strlen($name) > 100 || mb_strlen($description) > 255) {
            $this->redirect('admin_categories', 'cat_invalid');
            return;
        }

        $dup = $this->db->prepare('SELECT COUNT(*) FROM categories WHERE name = :name AND id_category <> :id');
        $dup->execute(['name' => $name, 'id' => $id]);
        if ((int) $dup->fetchColumn() > 0) {
            $this->redirect('admin_categories', 'cat_exists');
            return;
        }

        if ($id > 0) {
            $stmt = $this->db->prepare('UPDATE categories SET name = :name, description = :description WHERE id_category = :id');
            $stmt->execute(['name' => $name, 'description' => $description !== '' ? $description : null, 'id' => $id]);
            $this->redirect('admin_categories', 'cat_updated');
            return;
        }

        $stmt = $this->db->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');
        $stmt->execute(['name' => $name, 'description' => $description !== '' ? $description : null]);
        $this->redirect('admin_categories', 'cat_created');
    }

    public function deleteCategory(): void
    {
        AuthController::requireRole('admin');
        $this->requirePost();
        $this->requireCsrfOrDie();

        $id = (int) ($_POST['id_category'] ?? 0);
        $used = $this->db->prepare('SELECT COUNT(*) FROM items WHERE id_category = :id');
        $used->execute(['id' => $id]);
        if ((int) $used->fetchColumn() > 0) {
            $this->redirect('admin_categories', 'cat_in_use');
            return;
        }

        $stmt = $this->db->prepare('DELETE FROM categories WHERE id_category = :id');
        $stmt->execute(['id' => $id]);
        $this->redirect('admin_categories', 'cat_deleted');
    }

    public function showAdminMessages(): void
    {
        AuthController::requireRole('admin');

        $this->render('backoffice/admin_messages', [
            'adminPage'    => 'messages',
            'messages'     => $this->fetchMessages('1 = 1'),
            'pendingCount' => $this->countByStatus()['pending'],
            'notice'       => $this->getNotice($_GET['notice'] ?? ''),
        ]);
    }

    public function markMessageRead(): void
    {
        AuthController::requireRole('admin');
        $this->requirePost();
        $this->requireCsrfOrDie();

        $stmt = $this->db->prepare('UPDATE messages SET is_read = 1 WHERE id_message = :id');
        $stmt->execute(['id' => (int) ($_POST['id_message'] ?? 0)]);
        $this->redirect('admin_messages', 'message_read');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?action=dashboard');
        exit;
    }

    /* =====================================================
       Validation + upload
       ===================================================== */

    /** @return array{0: array<string,string>, 1: array<string,mixed>} */
    private function validateItem(array $post, array $files, bool $photoRequired): array
    {
        $errors = [];
        $clean = [
            'category'    => 0,
            'name'        => '',
            'description' => null,
            'type'        => 'lost',
            'date'        => '',
        ];

        $clean['name'] = trim((string) ($post['item_name'] ?? ''));
        if ($clean['name'] === '' || mb_strlen($clean['name']) > 150) {
            $errors['item_name'] = 'Enter an item name (150 characters maximum).';
        }

        $categoryId = (string) ($post['id_category'] ?? '');
        if (!ctype_digit($categoryId) || !$this->categoryExists((int) $categoryId)) {
            $errors['category'] = 'Please choose a category.';
        } else {
            $clean['category'] = (int) $categoryId;
        }

        $clean['type'] = (string) ($post['type'] ?? '');
        if (!in_array($clean['type'], self::TYPES, true)) {
            $errors['type'] = 'Choose Lost or Found.';
        }

        $clean['date'] = trim((string) ($post['date_found'] ?? ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $clean['date'])
            || !checkdate((int) substr($clean['date'], 5, 2), (int) substr($clean['date'], 8, 2), (int) substr($clean['date'], 0, 4))) {
            $errors['date_found'] = 'Enter a valid date (YYYY-MM-DD).';
        }

        $description = trim((string) ($post['description'] ?? ''));
        if (mb_strlen($description) > 500) {
            $errors['description'] = 'The description is limited to 500 characters.';
        } else {
            $clean['description'] = $description !== '' ? $description : null;
        }

        $file = $files['image'] ?? null;
        if ($this->hasUploadedPhoto($file)) {
            if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > self::MAX_PHOTO_SIZE) {
                $errors['image'] = 'The photo is too big (5 MB maximum).';
            } else {
                $mime = $this->detectMime($file['tmp_name']);
                if (!isset(self::ALLOWED_MIME[$mime]) || @getimagesize($file['tmp_name']) === false) {
                    $errors['image'] = 'Only JPG, PNG or WebP pictures are accepted.';
                }
            }
        } elseif ($photoRequired && $file !== null && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $errors['image'] = 'The photo upload failed. Please try again.';
        }

        return [$errors, $clean];
    }

    private function hasUploadedPhoto(?array $file): bool
    {
        return $file !== null && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    private function detectMime(string $path): string
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return (string) $finfo->file($path);
    }

    private function savePhoto(array $file): ?string
    {
        $ext = self::ALLOWED_MIME[$this->detectMime($file['tmp_name'])] ?? null;
        if ($ext === null || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        $dir = $this->rootDir . 'uploads/';
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return null;
        }

        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        return move_uploaded_file($file['tmp_name'], $dir . $name) ? 'uploads/' . $name : null;
    }

    private function deletePhotoFile(?string $storedPath): void
    {
        if (!$storedPath) return;
        $file = $this->rootDir . 'uploads/' . basename($storedPath);
        if (is_file($file)) @unlink($file);
    }

    /* =====================================================
       Queries
       ===================================================== */

    /** @return ItemModel[] */
    private function fetchItems(string $where, array $params, int $limit = 0): array
    {
        $sql = 'SELECT i.*, c.name AS category_name, u.username AS owner_name
                FROM items i
                JOIN categories c ON c.id_category = i.id_category
                JOIN users u ON u.id = i.owner_id
                WHERE ' . $where . '
                ORDER BY i.created_at DESC, i.id_item DESC';
        if ($limit > 0) $sql .= ' LIMIT ' . $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return array_map(fn(array $row) => ItemModel::fromArray($row), $stmt->fetchAll());
    }

    private function findItem(int $id): ?ItemModel
    {
        $rows = $this->fetchItems('i.id_item = :id', ['id' => $id]);
        return $rows[0] ?? null;
    }

    /** Own item AND still pending, otherwise null. */
    private function findEditableItem(int $id): ?ItemModel
    {
        $rows = $this->fetchItems('i.id_item = :id AND i.owner_id = :uid AND i.status = "pending"', [
            'id'  => $id,
            'uid' => $this->currentUserId(),
        ]);
        return $rows[0] ?? null;
    }

    /** @return CategoryModel[] */
    private function fetchCategories(): array
    {
        $rows = $this->db->query(
            'SELECT c.*, (SELECT COUNT(*) FROM items i WHERE i.id_category = c.id_category) AS item_count
             FROM categories c ORDER BY c.name'
        )->fetchAll();
        return array_map(fn(array $row) => CategoryModel::fromArray($row), $rows);
    }

    private function categoryExists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM categories WHERE id_category = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /** @return MessageModel[] */
    private function fetchMessages(string $where, array $params = []): array
    {
        $sql = 'SELECT m.*, i.item_name,
                       s.username AS sender_name, r.username AS receiver_name
                FROM messages m
                JOIN items i ON i.id_item = m.id_item
                JOIN users s ON s.id = m.sender_id
                JOIN users r ON r.id = m.receiver_id
                WHERE ' . $where . '
                ORDER BY m.date_sent DESC, m.id_message DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return array_map(fn(array $row) => MessageModel::fromArray($row), $stmt->fetchAll());
    }

    private function fetchUsers(int $excludeId): array
    {
        $stmt = $this->db->prepare('SELECT id, username, email, role FROM users WHERE id <> :id ORDER BY username');
        $stmt->execute(['id' => $excludeId]);
        return $stmt->fetchAll();
    }

    private function userExists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function countByStatus(): array
    {
        $rows = $this->db->query('SELECT status, COUNT(*) AS total FROM items GROUP BY status')->fetchAll();
        $counts = ['lost' => 0, 'found' => 0, 'pending' => 0, 'matched' => 0, 'returned' => 0];
        foreach ($rows as $row) {
            $counts[(string) $row['status']] = (int) $row['total'];
        }
        $types = $this->db->query('SELECT type, COUNT(*) AS total FROM items GROUP BY type')->fetchAll();
        foreach ($types as $row) {
            $counts[(string) $row['type']] = (int) $row['total'];
        }
        return $counts;
    }

    private function countUnreadMessages(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM messages WHERE receiver_id = :uid AND is_read = 0');
        $stmt->execute(['uid' => $this->currentUserId()]);
        return (int) $stmt->fetchColumn();
    }

    /* =====================================================
       Small helpers
       ===================================================== */

    private function emptyItemForm(): array
    {
        return [
            'id_item'     => 0,
            'id_category' => '',
            'item_name'   => '',
            'description' => '',
            'type'        => 'lost',
            'date_found'  => date('Y-m-d'),
            'image_url'   => '',
        ];
    }

    private function itemToForm(ItemModel $item): array
    {
        return [
            'id_item'     => $item->getId(),
            'id_category' => $item->getCategoryId(),
            'item_name'   => $item->getName(),
            'description' => $item->getDescription() ?? '',
            'type'        => $item->getType(),
            'date_found'  => $item->getDateFound(),
            'image_url'   => $item->getImageUrl() ?? '',
        ];
    }

    private function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    private function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('405 - Method not allowed');
        }
    }

    private function requireCsrfOrDie(): void
    {
        $token = (string) ($_POST['csrf_token'] ?? '');
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            exit('Invalid security token. Go back and try again.');
        }
    }

    private function getNotice(string $code): ?array
    {
        if (!isset(self::NOTICES[$code])) return null;
        return ['type' => self::NOTICES[$code][0], 'text' => self::NOTICES[$code][1]];
    }

    private function redirect(string $action, string $notice = '', array $extra = []): void
    {
        $query = ['action' => $action] + $extra;
        if ($notice !== '') $query['notice'] = $notice;
        header('Location: index.php?' . http_build_query($query));
        exit;
    }

    private function render(string $view, array $data = []): void
    {
        $data['csrfToken'] = $this->auth->getCsrfToken();
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
