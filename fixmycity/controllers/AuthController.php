<?php
require_once __DIR__ . '/../config/database.php';

class AuthController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    public function register(): void
    {
        self::requirePost();
        $this->verifyCsrfOrRedirect();
        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (mb_strlen($username) < 3 || mb_strlen($username) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || mb_strlen($email) > 150 || strlen($password) < 8 || $password !== (string) ($_POST['password_confirm'] ?? '')) {
            self::redirect('invalid');
        }
        try {
            $stmt = $this->db->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, \'user\')');
            $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') self::redirect('exists');
            throw $e;
        }
        self::redirect('registered');
    }

    public function login(): void
    {
        self::requirePost();
        $this->verifyCsrfOrRedirect();
        $identity = trim((string) ($_POST['identity'] ?? ''));
        $stmt = $this->db->prepare('SELECT id, username, email, password, role FROM users WHERE username = :name OR email = :email LIMIT 1');
        $stmt->execute(['name' => $identity, 'email' => $identity]);
        $user = $stmt->fetch();
        if (!$user || !password_verify((string) ($_POST['password'] ?? ''), $user['password'])) self::redirect('credentials');
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php?action=report_form');
        exit;
    }

    public function forgotPassword(): void
    {
        self::requirePost();
        $this->verifyCsrfOrRedirect();
        $email = trim((string) ($_POST['email'] ?? ''));
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $userId = $stmt->fetchColumn();
            if ($userId) {
                $token = bin2hex(random_bytes(32));
                $hash = hash('sha256', $token);
                $stmt = $this->db->prepare('INSERT INTO password_reset (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR)) ON DUPLICATE KEY UPDATE token_hash = VALUES(token_hash), expires_at = VALUES(expires_at)');
                $stmt->execute([$userId, $hash]);
                $link = $this->baseUrl() . '/index.html?reset=' . urlencode($token);
                $sent = @mail($email, 'FIXMYCITY password reset', "Use this link within one hour to reset your password:\n\n$link");
                if (!$sent && (($_SERVER['HTTP_HOST'] ?? '') === 'localhost' || str_starts_with($_SERVER['HTTP_HOST'] ?? '', 'localhost:'))) {
                    header('Location: ../index.html?message=sent&reset=' . urlencode($token));
                    exit;
                }
            }
        }
        self::redirect('sent');
    }

    public function resetPassword(): void
    {
        self::requirePost();
        $this->verifyCsrfOrRedirect();
        $token = (string) ($_POST['token'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        if (strlen($token) !== 64 || strlen($password) < 8) self::redirect('expired');
        $stmt = $this->db->prepare('SELECT user_id FROM password_reset WHERE token_hash = ? AND expires_at > NOW()');
        $stmt->execute([hash('sha256', $token)]);
        $userId = $stmt->fetchColumn();
        if (!$userId) self::redirect('expired');
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $userId]);
            $stmt = $this->db->prepare('DELETE FROM password_reset WHERE user_id = ?');
            $stmt->execute([$userId]);
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
        self::redirect('reset');
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: ../index.html');
        exit;
    }

    public static function isAuthenticated(): bool { return !empty($_SESSION['user_id']); }
    public static function hasRole(string $role): bool { return self::isAuthenticated() && ($_SESSION['role'] ?? null) === $role; }
    public static function requireAuth(): void { if (!self::isAuthenticated()) { header('Location: ../index.html'); exit; } }
    public static function requireRole(string $role): void { self::requireAuth(); if (($_SESSION['role'] ?? null) !== $role) { http_response_code(403); die('Access denied.'); } }

    public function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    private function verifyCsrfOrRedirect(): void
    {
        $token = (string) ($_POST['csrf_token'] ?? '');
        if ($token === '' || !hash_equals($this->getCsrfToken(), $token)) self::redirect('invalid');
    }

    private function baseUrl(): string
    {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        return ($https ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/fixmycity/index.php')), '/');
    }

    private static function requirePost(): void { if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); exit('Method not allowed'); } }
    private static function redirect(string $message): void { header('Location: ../index.html?message=' . urlencode($message)); exit; }
}
