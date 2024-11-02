<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

/**
 * Auth Routes
 */
$routes->get('login', 'AuthController::loginView');
$routes->post('login', 'AuthController::login');
$routes->get('register', 'AuthController::registerView');
$routes->post('register', 'AuthController::register');

/**
 * API Routes
 */
$routes->group('api', ['namespace' => 'App\Controllers\Api', 'filter' => 'auth-service'], function ($routes) {
    $routes->group('v1', function ($routes) {
        //Employee Department Routes
        $routes->group('employee', function ($routes) {
            //Employee Routes
            $routes->get('/', 'EmployeeController::index');
            $routes->post('/', 'EmployeeController::create', ['filter' => 'employee_store']);
            $routes->get('(:num)', 'EmployeeController::show/$1');
            $routes->post('(:num)', 'EmployeeController::update/$1', ['filter' => 'employee_store']);
            $routes->post('(:num)', 'EmployeeController::delete/$1');

            //Department Routes
            $routes->get('departments', 'EmployeeDepartmentController::index');
            $routes->post('departments', 'EmployeeDepartmentController::create', ['filter' => 'employee_department_store']);
            $routes->get('departments/(:num)', 'EmployeeDepartmentController::show/$1');
            $routes->post('departments/(:num)', 'EmployeeDepartmentController::update/$1', ['filter' => 'employee_department_store']);
            $routes->post('departments/(:num)', 'EmployeeDepartmentController::delete/$1');
        });
    });
});