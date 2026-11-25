<?php
/**
 * STUB - the real AuthController belongs to the auth teammate (workshop Sessions & Sécurité).
 * It only has the SAME public method names as the workshop's class, so that at merge time
 * you simply replace this file with theirs and nothing else changes.
 *
 * Dev behaviour: every call "logs in" a fake user from the seed data in config/fixmycity.sql
 * (id 1 = user, id 2 = admin). Change DEV_ROLE to 'admin' to test the back office.
 */
class AuthController
{
    private const DEV_ROLE = 'admin'; // 'user' | 'admin'

    public function __construct()
    {
        // real one: $this->db = Database::getConnexion();
    }

    private static function devLogin(): void
    {
        $_SESSION['user_id']  = self::DEV_ROLE === 'admin' ? 2 : 1;
        $_SESSION['username'] = self::DEV_ROLE === 'admin' ? 'admin1' : 'user1';
        $_SESSION['role']     = self::DEV_ROLE;
    }

    public static function isAuthenticated(): bool
    {
        self::devLogin();
        return !empty($_SESSION['user_id']);
    }

    public static function hasRole(string $role): bool
    {
        return self::isAuthenticated() && ($_SESSION['role'] ?? null) === $role;
    }

    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireAuth();
        if (($_SESSION['role'] ?? null) !== $role) {
            http_response_code(403);
            die('Access denied: you do not have the required permissions.');
        }
    }

    // same code as the workshop (7.4)
    public function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
