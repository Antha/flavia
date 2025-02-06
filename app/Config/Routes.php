<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');
$routes->get('/login','Login::index');
$routes->post('/login/authentication', 'Login::authentication');
$routes->get('/registration', 'Registration::index');

$routes->post('/sendEmail', 'Home::sendEmail');

//$routes->get('/report_summary','Scan_summary::index');
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('/login/logout', 'Login::logout');
    $routes->get('/home', 'Home::index');
    $routes->get('/report', 'Scan_summary::index');

    $routes->get('/qris', 'Qris::index');
    $routes->post('/qris/scrape', 'Qris::scrape');
    $routes->post('/qris/insert', 'Qris::insertData');

    $routes->get('/report/admin_report', 'Scan_summary::admin_report');
    $routes->post('/report/admin_report', 'Scan_summary::admin_report');
    $routes->get('/report/user_report', 'Scan_summary::user_report');
    $routes->post('/report/user_report', 'Scan_summary::user_report');
});
