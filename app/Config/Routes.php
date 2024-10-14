<?php

use App\Controllers\Auth;

/**
 * @var RouteCollection $routes
 */
$routes->set404Override('App\Controllers\Error::show404');

$routes->get('/', function () {
    return "Welcome to COOP Accounts Microservice!";
});

$routes->post('auth/authorize', [Auth::class, 'authorize']);
$routes->post('auth/refresh', [Auth::class, 'refresh']);
$routes->post('auth/request-otp', [Auth::class, 'requestOtp']);
$routes->post('auth/verify-opt-token', [Auth::class, 'verifyOtpOrToken']);
$routes->post('auth/reset-password', [Auth::class, 'resetPassword2']);
$routes->post('auth/reset-password/(:hash)', [Auth::class, 'resetPassword/$1']);
$routes->resource('registration', ['only'=>'create']);
$routes->resource('users');
$routes->resource('groups', ['except' => 'new,edit']);
$routes->resource('permissions', ['except' => 'new,edit']);
$routes->resource('roles', ['except' => 'new,edit']);
$routes->resource('resources', ['except' => 'new,edit']);
$routes->resource('user-roles', ['except' => 'new,edit']);
$routes->resource('user-groups', ['except' => 'new,edit']);
$routes->resource('group-roles', ['except' => 'new,edit']);

