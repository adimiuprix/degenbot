<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('trims', 'Home::telegram');
$routes->get('webhook', 'Home::setWebHook');