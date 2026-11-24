<?php

require_once __DIR__ . '/config/bootstrap.php';

$page = $_GET['page'] ?? 'front';

switch ($page) {
    // Front Office
    case 'front':
        (new FrontController())->dashboard();
        break;

    case 'front.services':
        (new FrontController())->services();
        break;

    case 'front.tickets':
        (new FrontController())->tickets();
        break;

    // Back Office - Categories
    case 'back.categories':
        (new BackCategorieController())->index();
        break;

    case 'back.categorie.create':
        (new BackCategorieController())->create();
        break;

    case 'back.categorie.edit':
        (new BackCategorieController())->edit();
        break;

    case 'back.categorie.delete':
        (new BackCategorieController())->delete();
        break;

    // Back Office - Services
    case 'back.services':
        (new BackServiceController())->index();
        break;

    case 'back.service.create':
        (new BackServiceController())->create();
        break;

    case 'back.service.edit':
        (new BackServiceController())->edit();
        break;

    case 'back.service.delete':
        (new BackServiceController())->delete();
        break;

    // Back Office - Tickets (read only)
    case 'back.tickets':
        (new BackTicketController())->index();
        break;

    default:
        (new HomeController())->index();
}
