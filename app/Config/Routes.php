<?php

use App\Controllers\AuthController;

/**
 * @var RouteCollection $routes
 */
$routes->set404Override('App\Controllers\Error::show404');

$routes->get('/', function () {
    return "Welcome to COOP Accounts Microservice!";
});

$routes->post('auth/authorize', [AuthController::class, 'authorize']);
$routes->post('auth/refresh', [AuthController::class, 'refresh']);
$routes->post('auth/request-otp', [AuthController::class, 'requestOtp']);
$routes->post('auth/verify-opt-token', [AuthController::class, 'verifyOtpOrToken']);
$routes->post('auth/reset-password', [AuthController::class, 'resetPassword2']);
$routes->post('auth/reset-password/(:hash)', [AuthController::class, 'resetPassword/$1']);
$routes->resource('registration', ['only' => 'create', 'controller' => 'RegistrationController']);
$routes->resource('users', ['execpt' => 'new,edit', 'controller' => 'UserController']);
$routes->resource('passwords', ['only' => 'update', 'controller' => 'PasswordController']);
$routes->resource('groups', ['except' => 'new,edit', 'controller' => "GroupController"]);
$routes->resource('permissions', ['except' => 'new,edit', 'controller' => 'PermissionController']);
$routes->resource('roles', ['except' => 'new,edit', 'controller' => 'RoleController']);
$routes->resource('resources', ['except' => 'new,edit', 'controller' => 'ResourcesController']);
$routes->resource('user-roles', ['except' => 'new,edit', 'controller' => 'UserRoleController']);
$routes->resource('user-groups', ['except' => 'new,edit', 'controller' => 'UserGroupController']);
$routes->resource('group-roles', ['except' => 'new,edit', 'controller' => 'GroupRoleController']);
