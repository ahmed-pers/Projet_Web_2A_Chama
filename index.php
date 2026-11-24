<?php

require_once __DIR__ . '/config/bootstrap.php';

$page = $_GET['page'] ?? 'front';

switch ($page) {
    case 'front':
        (new FrontController())->dashboard();
        break;

    case 'front.services':
        (new FrontController())->services();
        break;

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

    case 'back.assign':
        (new BackServiceController())->assign();
        break;

    case 'back.counters':
        (new BackCounterController())->index();
        break;

    case 'back.counter.create':
        (new BackCounterController())->create();
        break;

    case 'back.counter.edit':
        (new BackCounterController())->edit();
        break;

    case 'back.counter.delete':
        (new BackCounterController())->delete();
        break;

    default:
        (new HomeController())->index();
}
