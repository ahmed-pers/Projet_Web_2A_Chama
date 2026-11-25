<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ReportModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
// Controller: ALL the logic (PDO prepared statements, validation, upload, business rules).

/* ---------- tiny display helpers used by the views ---------- */
if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('report_location_label')) {
    function report_location_label(ReportModel $r): string
    {
        if ($r->getAddress() !== null && $r->getAddress() !== '') {
            return $r->getAddress();
        }
        if ($r->getLatitude() !== null && $r->getLongitude() !== null) {
            return $r->getLatitude() . ', ' . $r->getLongitude();
        }
        return '-';
    }
}

if (!function_exists('report_map_url')) {
    function report_map_url(ReportModel $r): string
    {
        if ($r->getLatitude() !== null && $r->getLongitude() !== null) {
            $lat = $r->getLatitude();
            $lng = $r->getLongitude();
            return "https://www.openstreetmap.org/?mlat=$lat&mlon=$lng#map=18/$lat/$lng";
        }
        return 'https://www.openstreetmap.org/search?query=' . urlencode((string) $r->getAddress());
    }
}

class ReportController
{
    private const MAX_PHOTO_SIZE = 5242880; // 5 MB
    private const ALLOWED_MIME   = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const STATUSES       = ['pending', 'in_progress', 'resolved'];
    // Status workflow: new status => the ONLY status it can come from (no going back)
    private const WORKFLOW       = ['in_progress' => 'pending', 'resolved' => 'in_progress'];
    // Points: 10 per report taken into account by the admin (in_progress or resolved)
    private const POINTS_PER_REPORT = 10;
    private const RANKS = [
        ['name' => 'Newcomer',       'min' => 0],
        ['name' => 'Active citizen', 'min' => 50],
        ['name' => 'Guardian',       'min' => 150],
        ['name' => 'City hero',      'min' => 300],
    ];
    private const NOTICES = [
        'sent'         => ['success', 'Your report was sent to the right department. Thank you!'],
        'updated'      => ['success', 'Your report was updated.'],
        'deleted'      => ['success', 'Report deleted.'],
        'not_editable' => ['error',   'This report does not exist, is not yours, or is no longer pending.'],
        'status_ok'    => ['success', 'Report status updated.'],
        'status_bad'   => ['error',   'This status change is not allowed (pending → in progress → resolved only).'],
        'cat_created'  => ['success', 'Category created.'],
        'cat_updated'  => ['success', 'Category updated.'],
        'cat_deleted'  => ['success', 'Category deleted.'],
        'cat_invalid'  => ['error',   'A category name (100 characters max) and a department are required.'],
        'cat_exists'   => ['error',   'A category with this name already exists.'],
        'cat_in_use'   => ['error',   'This category is used by reports and cannot be deleted.'],
    ];

    private PDO $db;
    private AuthController $auth;
    private string $rootDir;

    public function __construct(AuthController $auth)
    {
        $this->db      = Database::getConnexion();
        $this->auth    = $auth;
        $this->rootDir = dirname(__DIR__) . '/';
    }

    /* =====================================================
       FRONT OFFICE (role: any authenticated user)
       ===================================================== */

    /** CREATE - form */
    public function showReportForm(array $errors = [], array $old = []): void
    {
        AuthController::requireAuth();
        $this->render('frontoffice/report_form', [
            'categories' => $this->fetchCategories(),
            'errors'     => $errors,
            'old'        => $old,
        ] + $this->panelData());
    }

    /** CREATE - submit */
    public function createReport(): void
    {
        AuthController::requireAuth();
        $this->requirePost();

        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            $this->showReportForm(['form' => 'Invalid security token. Please try again.'], $_POST);
            return;
        }

        [$errors, $clean] = $this->validateReport($_POST, $_FILES);
        if ($errors) {
            http_response_code(422);
            $this->showReportForm($errors, $_POST);
            return;
        }

        $photoPath = $this->savePhoto($_FILES['photo']);
        if ($photoPath === null) {
            http_response_code(500);
            $this->showReportForm(['photo' => 'The photo could not be saved. Please try again.'], $_POST);
            return;
        }

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO report (user_id, category_id, photo, address, latitude, longitude, description)
                 VALUES (:user_id, :category_id, :photo, :address, :latitude, :longitude, :description)'
            );
            $stmt->execute([
                'user_id'     => $this->currentUserId(),
                'category_id' => $clean['category_id'],
                'photo'       => $photoPath,
                'address'     => $clean['address'],
                'latitude'    => $clean['latitude'],
                'longitude'   => $clean['longitude'],
                'description' => $clean['description'],
            ]);
        } catch (PDOException $ex) {
            @unlink($this->rootDir . $photoPath); // no orphan file
            http_response_code(500);
            $this->showReportForm(['form' => 'The report could not be saved. Please try again.'], $_POST);
            return;
        }

        $this->redirect('reports', 'sent', ['mine' => 1]);
    }

    /** READ - "All reports" (default) or "My reports" (?mine=1), with the category -> department join */
    public function showReports(): void
    {
        AuthController::requireAuth();

        $mine = !empty($_GET['mine']);
        $reports = $mine
            ? $this->fetchReports('r.user_id = :uid', ['uid' => $this->currentUserId()])
            : $this->fetchReports('1 = 1', []);

        $this->render('frontoffice/reports', [
            'reports'       => $reports,
            'mine'          => $mine,
            'currentUserId' => $this->currentUserId(),
            'notice'        => $this->getNotice($_GET['notice'] ?? ''),
        ] + $this->panelData());
    }

    /** UPDATE - form (own report, still pending) */
    public function showEditReport(): void
    {
        AuthController::requireAuth();

        $report = $this->findEditableReport((int) ($_GET['id'] ?? 0));
        if ($report === null) {
            $this->redirect('reports', 'not_editable', ['mine' => 1]);
            return;
        }
        $this->renderEditForm($report, [], []);
    }

    /** UPDATE - submit (description and category only) */
    public function updateReport(): void
    {
        AuthController::requireAuth();
        $this->requirePost();
        $this->requireCsrfOrDie();

        $report = $this->findEditableReport((int) ($_POST['report_id'] ?? 0));
        if ($report === null) {
            $this->redirect('reports', 'not_editable', ['mine' => 1]);
            return;
        }

        $errors = [];
        $categoryId = (string) ($_POST['category_id'] ?? '');
        if (!ctype_digit($categoryId) || !$this->categoryExists((int) $categoryId)) {
            $errors['category'] = 'Please choose a category.';
        }
        $description = trim((string) ($_POST['description'] ?? ''));
        if (mb_strlen($description) > 500) {
            $errors['description'] = 'The description is limited to 500 characters.';
        }
        if ($errors) {
            http_response_code(422);
            $this->renderEditForm($report, $errors, $_POST);
            return;
        }

        $stmt = $this->db->prepare(
            "UPDATE report SET category_id = :cat, description = :descr
             WHERE id = :id AND user_id = :uid AND status = 'pending'"
        );
        $stmt->execute([
            'cat'   => (int) $categoryId,
            'descr' => $description !== '' ? $description : null,
            'id'    => $report->getId(),
            'uid'   => $this->currentUserId(),
        ]);

        $this->redirect('reports', 'updated', ['mine' => 1]);
    }

    /** DELETE - own report, only while pending */
    public function deleteReport(): void
    {
        AuthController::requireAuth();
        $this->requirePost();
        $this->requireCsrfOrDie();

        $report = $this->findEditableReport((int) ($_POST['report_id'] ?? 0));
        if ($report === null) {
            $this->redirect('reports', 'not_editable', ['mine' => 1]);
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM report WHERE id = :id AND user_id = :uid AND status = 'pending'");
        $stmt->execute(['id' => $report->getId(), 'uid' => $this->currentUserId()]);
        $this->deletePhotoFile($report->getPhoto());

        $this->redirect('reports', 'deleted', ['mine' => 1]);
    }

    public function showInfo(): void
    {
        $this->render('frontoffice/info');
    }

    /* =====================================================
       BACK OFFICE (role: admin)
       ===================================================== */

    /** READ - all reports with the join, filters */
    public function showAdminReports(): void
    {
        AuthController::requireRole('admin');

        // no "status" in the URL => pending by default; empty "status" => all
        $status = array_key_exists('status', $_GET) ? (string) $_GET['status'] : 'pending';
        if (!in_array($status, self::STATUSES, true)) {
            $status = '';
        }
        $view = (string) ($_GET['view'] ?? 'all');
        if (!in_array($view, ['all', 'today', 'week', 'month'], true)) {
            $view = 'all';
        }
        $filters = [
            'q'      => trim((string) ($_GET['q'] ?? '')),
            'type'   => ctype_digit((string) ($_GET['type'] ?? '')) ? (int) $_GET['type'] : 0,
            'status' => $status,
            'view'   => $view,
        ];

        $where  = ['1 = 1'];
        $params = [];

        if ($filters['q'] !== '') {
            $where[] = '(r.address LIKE :q1 OR r.description LIKE :q2 OR c.name LIKE :q3 OR d.name LIKE :q4 OR u.username LIKE :q5)';
            $like = '%' . $filters['q'] . '%';
            foreach (['q1', 'q2', 'q3', 'q4', 'q5'] as $k) {
                $params[$k] = $like;
            }
        }
        if ($filters['type'] > 0) {
            $where[] = 'r.category_id = :type';
            $params['type'] = $filters['type'];
        }
        if ($filters['status'] !== '') {
            $where[] = 'r.status = :status';
            $params['status'] = $filters['status'];
        }
        if ($filters['view'] === 'today') {
            $where[] = 'DATE(r.created_at) = CURDATE()';
        } elseif ($filters['view'] === 'week') {
            $where[] = 'YEARWEEK(r.created_at, 1) = YEARWEEK(CURDATE(), 1)';
        } elseif ($filters['view'] === 'month') {
            $where[] = 'YEAR(r.created_at) = YEAR(CURDATE()) AND MONTH(r.created_at) = MONTH(CURDATE())';
        }

        $this->render('backoffice/admin_reports', [
            'reports'      => $this->fetchReports(implode(' AND ', $where), $params),
            'categories'   => $this->fetchCategories(),
            'filters'      => $filters,
            'pendingCount' => $this->countPending(),
            'notice'       => $this->getNotice($_GET['notice'] ?? ''),
            'adminPage'    => 'reports',
        ]);
    }

    /** UPDATE status: only pending -> in_progress -> resolved, no going back */
    public function changeReportStatus(): void
    {
        AuthController::requireRole('admin');
        $this->requirePost();
        $this->requireCsrfOrDie();

        $id = (int) ($_POST['report_id'] ?? 0);
        $to = (string) ($_POST['to'] ?? '');

        if (!isset(self::WORKFLOW[$to])) {
            $this->redirect('admin_reports', 'status_bad');
            return;
        }

        $stmt = $this->db->prepare('UPDATE report SET status = :to WHERE id = :id AND status = :from');
        $stmt->execute(['to' => $to, 'id' => $id, 'from' => self::WORKFLOW[$to]]);

        $this->redirect('admin_reports', $stmt->rowCount() > 0 ? 'status_ok' : 'status_bad', ['status' => '']);
    }

    /** DELETE any report */
    public function deleteAdminReport(): void
    {
        AuthController::requireRole('admin');
        $this->requirePost();
        $this->requireCsrfOrDie();

        $id = (int) ($_POST['report_id'] ?? 0);

        $sel = $this->db->prepare('SELECT photo FROM report WHERE id = :id');
        $sel->execute(['id' => $id]);
        $photo = $sel->fetchColumn();

        $stmt = $this->db->prepare('DELETE FROM report WHERE id = :id');
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() > 0 && $photo) {
            $this->deletePhotoFile((string) $photo);
        }

        $this->redirect('admin_reports', 'deleted', ['status' => '']);
    }

    /* ---------- categories (problem_category + department join) ---------- */

    public function showAdminCategories(): void
    {
        AuthController::requireRole('admin');

        $stmt = $this->db->query(
            'SELECT c.*, d.name AS department_name, d.contact_email AS department_email,
                    (SELECT COUNT(*) FROM report r WHERE r.category_id = c.id) AS report_count
             FROM problem_category c
             JOIN department d ON d.id = c.department_id
             ORDER BY c.name'
        );
        $categories = array_map(fn(array $row) => CategoryModel::fromArray($row), $stmt->fetchAll());

        $this->render('backoffice/admin_categories', [
            'categories'   => $categories,
            'departments'  => $this->db->query('SELECT id, name, contact_email FROM department ORDER BY name')->fetchAll(),
            'pendingCount' => $this->countPending(),
            'notice'       => $this->getNotice($_GET['notice'] ?? ''),
            'adminPage'    => 'categories',
        ]);
    }

    /** Create (no id) or update (id) a category */
    public function saveCategory(): void
    {
        AuthController::requireRole('admin');
        $this->requirePost();
        $this->requireCsrfOrDie();

        $id           = (int) ($_POST['id'] ?? 0);
        $name         = trim((string) ($_POST['name'] ?? ''));
        $departmentId = (string) ($_POST['department_id'] ?? '');

        $deptOk = false;
        if (ctype_digit($departmentId)) {
            $chk = $this->db->prepare('SELECT COUNT(*) FROM department WHERE id = :id');
            $chk->execute(['id' => (int) $departmentId]);
            $deptOk = (int) $chk->fetchColumn() > 0;
        }
        if ($name === '' || mb_strlen($name) > 100 || !$deptOk) {
            $this->redirect('admin_categories', 'cat_invalid');
            return;
        }

        // category names are unique
        $dup = $this->db->prepare('SELECT COUNT(*) FROM problem_category WHERE name = :name AND id <> :id');
        $dup->execute(['name' => $name, 'id' => $id]);
        if ((int) $dup->fetchColumn() > 0) {
            $this->redirect('admin_categories', 'cat_exists');
            return;
        }

        if ($id > 0) {
            $stmt = $this->db->prepare('UPDATE problem_category SET name = :name, department_id = :dept WHERE id = :id');
            $stmt->execute(['name' => $name, 'dept' => (int) $departmentId, 'id' => $id]);
            $this->redirect('admin_categories', 'cat_updated');
        } else {
            $stmt = $this->db->prepare('INSERT INTO problem_category (name, department_id) VALUES (:name, :dept)');
            $stmt->execute(['name' => $name, 'dept' => (int) $departmentId]);
            $this->redirect('admin_categories', 'cat_created');
        }
    }

    /** A category used by reports cannot be deleted */
    public function deleteCategory(): void
    {
        AuthController::requireRole('admin');
        $this->requirePost();
        $this->requireCsrfOrDie();

        $id = (int) ($_POST['id'] ?? 0);

        $used = $this->db->prepare('SELECT COUNT(*) FROM report WHERE category_id = :id');
        $used->execute(['id' => $id]);
        if ((int) $used->fetchColumn() > 0) {
            $this->redirect('admin_categories', 'cat_in_use');
            return;
        }

        $stmt = $this->db->prepare('DELETE FROM problem_category WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $this->redirect('admin_categories', 'cat_deleted');
    }

    /* =====================================================
       Validation + upload
       ===================================================== */

    /** @return array{0: array<string,string>, 1: array<string,mixed>} [errors, clean values] */
    private function validateReport(array $post, array $files): array
    {
        $errors = [];
        $clean  = ['category_id' => 0, 'address' => null, 'latitude' => null, 'longitude' => null, 'description' => null];

        // 1. photo: required, real type (finfo), jpg/png/webp, 5 MB max
        $file = $files['photo'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $errors['photo'] = 'Please add a photo.';
        } elseif ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE
                 || $file['size'] > self::MAX_PHOTO_SIZE) {
            $errors['photo'] = 'The photo is too big (5 MB maximum).';
        } elseif ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            $errors['photo'] = 'The photo upload failed. Please try again.';
        } else {
            $mime = $this->detectMime($file['tmp_name']);
            if (!isset(self::ALLOWED_MIME[$mime]) || @getimagesize($file['tmp_name']) === false) {
                $errors['photo'] = 'Only JPG, PNG or WebP pictures are accepted.';
            }
        }

        // 2. location: at least an address OR coordinates in range
        $address = trim((string) ($post['address'] ?? ''));
        $lat     = trim((string) ($post['latitude'] ?? ''));
        $lng     = trim((string) ($post['longitude'] ?? ''));

        if ($lat !== '' || $lng !== '') {
            if (!is_numeric($lat) || !is_numeric($lng)
                || (float) $lat < -90 || (float) $lat > 90 || (float) $lng < -180 || (float) $lng > 180) {
                $errors['location'] = 'Enter a valid latitude (-90 to 90) and longitude (-180 to 180).';
            } else {
                $clean['latitude']  = round((float) $lat, 6);
                $clean['longitude'] = round((float) $lng, 6);
            }
        } elseif ($address === '') {
            $errors['location'] = 'Please give an address or coordinates.';
        } elseif (mb_strlen($address) > 255) {
            $errors['location'] = 'The address is too long (255 characters maximum).';
        } else {
            $clean['address'] = $address;
        }

        // 3. the category must exist
        $categoryId = (string) ($post['category_id'] ?? '');
        if (!ctype_digit($categoryId) || !$this->categoryExists((int) $categoryId)) {
            $errors['category'] = 'Please choose a category.';
        } else {
            $clean['category_id'] = (int) $categoryId;
        }

        // 4. optional description, 500 characters max
        $description = trim((string) ($post['description'] ?? ''));
        if (mb_strlen($description) > 500) {
            $errors['description'] = 'The description is limited to 500 characters.';
        } elseif ($description !== '') {
            $clean['description'] = $description;
        }

        return [$errors, $clean];
    }

    private function detectMime(string $path): string
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return (string) $finfo->file($path);
    }

    /** Moves the picture to /uploads with a random name. Returns the relative path (uploads/xxx.jpg) or null. */
    private function savePhoto(array $file): ?string
    {
        $ext = self::ALLOWED_MIME[$this->detectMime($file['tmp_name'])] ?? null;
        if ($ext === null) {
            return null;
        }
        $dir = $this->rootDir . 'uploads/';
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return null;
        }
        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        return move_uploaded_file($file['tmp_name'], $dir . $name) ? 'uploads/' . $name : null;
    }

    private function deletePhotoFile(string $storedPath): void
    {
        $file = $this->rootDir . 'uploads/' . basename($storedPath);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    /* =====================================================
       Queries
       ===================================================== */

    /** @return ReportModel[] (report + category + department + author) */
    private function fetchReports(string $where, array $params, int $limit = 0): array
    {
        $sql = 'SELECT r.*, c.name AS category_name, d.name AS department_name,
                       d.contact_email AS department_email, u.username
                FROM report r
                JOIN problem_category c ON c.id = r.category_id
                JOIN department d ON d.id = c.department_id
                LEFT JOIN users u ON u.id = r.user_id
                WHERE ' . $where . ' ORDER BY r.created_at DESC, r.id DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return array_map(fn(array $row) => ReportModel::fromArray($row), $stmt->fetchAll());
    }

    /** Own report AND still pending, otherwise null (business rule for edit / delete) */
    private function findEditableReport(int $id): ?ReportModel
    {
        $rows = $this->fetchReports("r.id = :id AND r.user_id = :uid AND r.status = 'pending'", [
            'id'  => $id,
            'uid' => $this->currentUserId(),
        ]);
        return $rows[0] ?? null;
    }

    /** @return CategoryModel[] (with the department it is routed to) */
    private function fetchCategories(): array
    {
        $rows = $this->db->query(
            'SELECT c.*, d.name AS department_name, d.contact_email AS department_email
             FROM problem_category c JOIN department d ON d.id = c.department_id
             ORDER BY c.id'
        )->fetchAll();
        return array_map(fn(array $row) => CategoryModel::fromArray($row), $rows);
    }

    private function categoryExists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM problem_category WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function countPending(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM report WHERE status = 'pending'")->fetchColumn();
    }

    /** Right panel: points and rank are computed, no extra column needed */
    private function panelData(): array
    {
        $uid = $this->currentUserId();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM report WHERE user_id = :uid AND status IN ('in_progress', 'resolved')");
        $stmt->execute(['uid' => $uid]);
        $points = (int) $stmt->fetchColumn() * self::POINTS_PER_REPORT;

        $rank = self::RANKS[0];
        $next = null;
        foreach (self::RANKS as $i => $r) {
            if ($points >= $r['min']) {
                $rank = $r;
                $next = self::RANKS[$i + 1] ?? null;
            }
        }
        $progress = $next === null
            ? 100
            : (int) floor(($points - $rank['min']) / ($next['min'] - $rank['min']) * 100);

        return [
            'user' => [
                'name'     => (string) ($_SESSION['username'] ?? ''),
                'photo'    => 'assets/imgs/avatar.png',
                'points'   => $points,
                'rank'     => $rank['name'],
                'progress' => $progress,
            ],
            'latestReports' => $this->fetchReports('r.user_id = :uid', ['uid' => $uid], 3),
        ];
    }

    /* =====================================================
       Small helpers
       ===================================================== */

    private function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    // The workshop's verifyCsrfToken() is private in AuthController, so the same check lives here
    // (same session key 'csrf_token', same hash_equals): it keeps working after the merge.
    private function verifyCsrfToken(?string $token): bool
    {
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    private function requireCsrfOrDie(): void
    {
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid security token. Go back and try again.');
        }
    }

    private function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('405 - Method not allowed');
        }
    }

    /** @return array{type:string,text:string}|null */
    private function getNotice(string $code): ?array
    {
        if (!isset(self::NOTICES[$code])) {
            return null;
        }
        return ['type' => self::NOTICES[$code][0], 'text' => self::NOTICES[$code][1]];
    }

    private function redirect(string $action, string $notice, array $extra = []): void
    {
        $query = ['action' => $action] + $extra + ['notice' => $notice];
        header('Location: index.php?' . http_build_query($query));
        exit;
    }

    private function renderEditForm(ReportModel $report, array $errors, array $old): void
    {
        $this->render('frontoffice/report_edit', [
            'report'     => $report,
            'categories' => $this->fetchCategories(),
            'errors'     => $errors,
            'old'        => $old,
        ] + $this->panelData());
    }

    private function render(string $view, array $data = []): void
    {
        $data['csrfToken'] = $this->auth->getCsrfToken();
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
