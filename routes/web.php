<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


// use Illuminate\Support\Str;

// $router->get('/key', function() {
//     return Str::random(32);
// });

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//user
$router->post('/user/signup', 'UserController@create');
$router->get('/user/activation', 'UserController@activate');
$router->post('/user/login', 'UserController@login');
$router->post('/user/forget-password', 'UserController@resetpasswd');

// $router->group(['middleware' => 'auth.jwt'], function () use ($router) {
$router->group(['middleware' => 'auth'], function () use ($router) {
    //admin
    $router->get('/user/alluser', 'AdminController@showAllDataUser');
    $router->get('/user/{id:[0-9]+}', 'AdminController@showDetailDataUser');
    //user
    $router->put('/user/{id:[0-9]+}', 'UserController@update');
    $router->delete('/user/{id:[0-9]+}', 'UserController@delete');
    //node
    $router->post('/node', 'NodeController@create');
    $router->get('/node', 'NodeController@showAll');
    $router->get('/node/{id:[0-9]+}', 'NodeController@showDetailData');
    $router->put('/node/{id:[0-9]+}', 'NodeController@update');
    $router->delete('/node/{id:[0-9]+}', 'NodeController@delete');
    //hardware
    $router->post('/hardware', 'HardwareController@create');
    $router->get('/hardware', 'HardwareController@showAll');
    $router->get('/hardware/{id:[0-9]+}', 'HardwareController@showDetailData');
    $router->put('/hardware/{id:[0-9]+}', 'HardwareController@update');
    $router->delete('/hardware/{id:[0-9]+}', 'HardwareController@delete');
    //sensor
    $router->post('/sensor', 'SensorController@create');
    $router->get('/sensor', 'SensorController@showAll');
    $router->get('/sensor/{id:[0-9]+}', 'SensorController@showDetailData');
    $router->put('/sensor/{id:[0-9]+}', 'SensorController@update');
    $router->delete('/sensor/{id:[0-9]+}', 'SensorController@delete');
    //channel/feed
    $router->post('/channel', 'FeedController@create');
});