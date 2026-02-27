<?php
// public/index.php

session_start();

require_once __DIR__ . '/../app/controllers/GuestController.php';

$controller = new GuestController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'store':
        $controller->store();
        break;

    case 'edit':
        $controller->edit();   // handle GET (tampil form) & POST (update)
        break;

    case 'destroy':
        $controller->destroy();
        break;

    case 'index':
    default:
        $controller->index();
        break;
}