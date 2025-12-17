<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\AdminController;
use App\Controllers\NotificationController;
use App\Controllers\Api\UpdateController;
use App\Controllers\Api\NewspaperController;
use App\Controllers\Api\ServiceController;

/**
 * @var RouteCollection $routes
 */


$routes->get('/', 'AdminController::index');
$routes->post('login', 'AdminController::login');
$routes->get('logout', 'AdminController::logout');
$routes->get('dashboard', 'AdminController::dashboard');

$routes->get('updates', 'AdminController::updates'); // List all updates
$routes->get('updates/download', 'AdminController::downloadUpdatesPdf');
$routes->match(['get', 'post'], 'updates/add', 'AdminController::addUpdate'); // Add new update
$routes->match(['get', 'post'], 'updates/edit/(:num)', 'AdminController::editUpdate/$1'); // Edit update
$routes->get('updates/delete/(:num)', 'AdminController::deleteUpdate/$1'); // Delete update
$routes->get('updates/deleteFile/(:num)/(:num)', 'AdminController::deleteFile/$1/$2'); // Delete file from update

$routes->get('newspapers', 'AdminController::newspapers');
$routes->match(['get', 'post'], 'newspapers/add', 'AdminController::addNewspaper');
$routes->match(['get', 'post'], 'newspapers/edit/(:num)', 'AdminController::editNewspaper/$1');
$routes->get('newspapers/delete/(:num)', 'AdminController::deleteNewspaper/$1');
$routes->get('newspapers/deleteFile/(:num)/(:any)', 'AdminController::deleteNewspaperFile/$1/$2');


$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // 🔹 Updates (Public / API)
    $routes->get('updates', 'UpdateController::getUpdates'); // Get all updates
    $routes->get('updates/(:num)', 'UpdateController::getUpdate/$1'); // Get single update by ID

    // 🔹 Newspapers (Public / API)
    $routes->get('newspapers', 'NewspaperController::getNewspapers');
    $routes->get('newspapers/(:num)', 'NewspaperController::getNewspaper/$1');

    $routes->post('service-request', 'ServiceController::sendRequest');
});
