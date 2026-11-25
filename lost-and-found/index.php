<?php
declare(strict_types=1);

// Same session hardening and security headers as the FIXMYCITY module.
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
session_start();

$sessionLifetime = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
    $_SESSION = [];
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:");

require_once __DIR__ . '/controllers/ItemController.php';

$controller = new ItemController();
$action = (string) ($_GET['action'] ?? 'dashboard');

switch ($action) {
    // ---------- FRONT OFFICE ----------
    case 'dashboard':
        $controller->dashboard();
        break;
    case 'report_form':
        $controller->showReportForm();
        break;
    case 'item_create':
        $controller->createItem();
        break;
    case 'items':
        $controller->showItems();
        break;
    case 'item_details':
        $controller->showItemDetails();
        break;
    case 'my_items':
        $controller->showMyItems();
        break;
    case 'item_edit':
        $controller->showEditItem();
        break;
    case 'item_update':
        $controller->updateItem();
        break;
    case 'item_delete':
        $controller->deleteItem();
        break;
    case 'message_form':
        $controller->showMessageForm();
        break;
    case 'message_create':
        $controller->createMessage();
        break;
    case 'messages':
        $controller->showMessages();
        break;

    // ---------- BACK OFFICE ----------
    case 'admin_items':
        $controller->showAdminItems();
        break;
    case 'admin_item_status':
        $controller->changeItemStatus();
        break;
    case 'admin_item_delete':
        $controller->deleteAdminItem();
        break;
    case 'admin_categories':
        $controller->showAdminCategories();
        break;
    case 'admin_category_save':
        $controller->saveCategory();
        break;
    case 'admin_category_delete':
        $controller->deleteCategory();
        break;
    case 'admin_messages':
        $controller->showAdminMessages();
        break;
    case 'admin_message_read':
        $controller->markMessageRead();
        break;

    case 'logout':
        $controller->logout();
        break;

    default:
        http_response_code(404);
        echo '404 - Page not found';
        break;
}
