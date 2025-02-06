<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('/auth/login','Auth::login');
$routes->post('/auth/cekLogin', 'Auth::cekLogin');
$routes->get('/registration', 'Registration::index');

$routes->get('/qris', 'Qris::index');
$routes->post('/qris/scrape', 'Qris::scrape');
//$routes->get('/report_summary','Scan_summary::index');
