<?php

// Simple, explicit loading: easy to see which file does what.
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/models/Model.php';
require_once __DIR__ . '/app/models/Categorie.php';
require_once __DIR__ . '/app/models/Service.php';
require_once __DIR__ . '/app/models/Ticket.php';
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/FrontController.php';
require_once __DIR__ . '/app/controllers/BackCategorieController.php';
require_once __DIR__ . '/app/controllers/BackServiceController.php';
require_once __DIR__ . '/app/controllers/BackTicketController.php';

$page = $_GET['page'] ?? 'front';

if ($page === 'front') {
    (new FrontController())->dashboard();
} elseif ($page === 'front.services') {
    (new FrontController())->services();
} elseif ($page === 'front.tickets') {
    (new FrontController())->tickets();
} elseif ($page === 'back.categories') {
    (new BackCategorieController())->index();
} elseif ($page === 'back.categorie.create') {
    (new BackCategorieController())->create();
} elseif ($page === 'back.categorie.edit') {
    (new BackCategorieController())->edit();
} elseif ($page === 'back.categorie.delete') {
    (new BackCategorieController())->delete();
} elseif ($page === 'back.services') {
    (new BackServiceController())->index();
} elseif ($page === 'back.service.create') {
    (new BackServiceController())->create();
} elseif ($page === 'back.service.edit') {
    (new BackServiceController())->edit();
} elseif ($page === 'back.service.delete') {
    (new BackServiceController())->delete();
} elseif ($page === 'back.tickets') {
    (new BackTicketController())->index();
} else {
    (new HomeController())->index();
}
