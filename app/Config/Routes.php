<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');
$routes->get('/login','Login::index');
$routes->post('/login/authentication', 'Login::authentication');
$routes->get('/registration', 'Registration::index');
$routes->get('/registration/success', 'Registration::success');
$routes->post('/registration/auth', 'Registration::auth');
$routes->get('/registration/verify/(:any)', 'Registration::verify/$1');
$routes->post('/registration/update', 'Registration::update');
$routes->post('/sendEmail', 'Home::sendEmail');

//$routes->get('/report_summary','Scan_summary::index');
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('/login/logout', 'Login::logout');
    $routes->get('/home', 'Home::index');
    $routes->get('/report', 'Scan_summary::index');

    $routes->get('/qris', 'Qris::index', ['filter' => 'checkSession']);
    $routes->post('/qris/scrape', 'Qris::scrape', ['filter' => 'checkSession']);
    $routes->post('/qris/insert', 'Qris::insertData', ['filter' => 'checkSession']);

    //using manual compare
    //$routes->get('/report/admin_report', 'Scan_summary::admin_report');
    //$routes->post('/report/admin_report', 'Scan_summary::admin_report');
    //$routes->get('/report/user_report', 'Scan_summary::user_report', ['filter' => 'checkSession']);
    //$routes->post('/report/user_report', 'Scan_summary::user_report',['filter' => 'checkSession']);

    //using real time compare
    $routes->get('/report/admin_report', 'Scan_summary::admin_report_real_time');
    $routes->post('/report/admin_report', 'Scan_summary::admin_report_real_time');
    $routes->get('/report/user_report', 'Scan_summary::user_report_real_time');
    $routes->post('/report/user_report', 'Scan_summary::user_report_real_time');
});
