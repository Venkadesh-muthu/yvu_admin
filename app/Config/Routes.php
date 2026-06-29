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
$routes->get('newspapers/deleteImage/(:num)', 'AdminController::deleteNewspaperFile/$1');

$routes->get('events', 'AdminController::events');
$routes->match(['get', 'post'], 'events/add', 'AdminController::addEvent');
$routes->match(['get', 'post'], 'events/edit/(:num)', 'AdminController::editEvent/$1');
$routes->get('events/delete/(:num)', 'AdminController::deleteEvent/$1');
$routes->get('events/deleteImage/(:num)', 'AdminController::deleteEventImage/$1');

$routes->get('gallery', 'AdminController::gallery');
$routes->match(['get', 'post'], 'gallery/add', 'AdminController::addGallery');
$routes->match(['get', 'post'], 'gallery/edit/(:num)', 'AdminController::editGallery/$1');
$routes->get('gallery/delete/(:num)', 'AdminController::deleteGallery/$1');
$routes->get('gallery/deleteImage/(:num)', 'AdminController::deleteGalleryImage/$1');

$routes->get('visitors', 'AdminController::visitors');
$routes->match(['get', 'post'], 'visitors/add', 'AdminController::addVisitor');
$routes->match(['get', 'post'], 'visitors/edit/(:num)', 'AdminController::editVisitor/$1');
$routes->get('visitors/delete/(:num)', 'AdminController::deleteVisitor/$1');
$routes->get('visitors/deleteImage/(:num)', 'AdminController::deleteVisitorImage/$1');

$routes->get('vcs-programs', 'AdminController::vcsPrograms');
$routes->match(['get', 'post'], 'vcs-programs/add', 'AdminController::addVcsProgram');
$routes->match(['get', 'post'], 'vcs-programs/edit/(:num)', 'AdminController::editVcsProgram/$1');
$routes->get('vcs-programs/delete/(:num)', 'AdminController::deleteVcsProgram/$1');
$routes->get('vcs-programs/deleteImage/(:num)', 'AdminController::deleteVcsProgramImage/$1');


$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // 🔹 Updates (Public / API)
    $routes->get('updates', 'UpdateController::getUpdates'); // Get currently active updates
    $routes->get('allupdates', 'UpdateController::getAllUpdates'); // Get all updates without date filtering
    $routes->get('updates/(:num)', 'UpdateController::getUpdate/$1'); // Get single update by ID

    // 🔹 Newspapers (Public / API)
    $routes->get('newspapers', 'NewspaperController::getNewspapers');
    $routes->get('allnewspapers', 'NewspaperController::allNewspapers');
    $routes->get('newspapers/(:num)', 'NewspaperController::getNewspaper/$1');

    $routes->post('service-request', 'ServiceController::sendRequest');

    // 🔹 Events
    $routes->get('events', 'EventController::getEvents');
    $routes->get('events/(:num)', 'EventController::getEvent/$1');

    // ===============================
    // 🔹 Gallery
    // ===============================
    $routes->get('galleries', 'GalleryController::getGalleries');
    $routes->get('galleries/(:num)', 'GalleryController::getGallery/$1');

    // ===============================
    // 🔹 Visitors
    // ===============================
    $routes->get('visitors', 'VisitorController::getVisitors');
    $routes->get('visitors/(:num)', 'VisitorController::getVisitor/$1');

    // ===============================
    // 🔹 VC Programs
    // ===============================
    $routes->get('programs', 'VcsProgramController::getPrograms');
    $routes->get('programs/(:num)', 'VcsProgramController::getProgram/$1');
});
