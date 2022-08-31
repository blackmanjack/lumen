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


use Illuminate\Support\Str;

$router->get('/key', function() {
    return Str::random(32);
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//user
$router->post('/user/signup', 'UserController@create');
$router->get('/user/activation', 'UserController@activate');
$router->post('/user/login', 'UserController@login');
$router->post('/user/forget-password', 'UserController@resetpasswd');

$router->group(['middleware' => 'auth'], function () use ($router) {
    //admin
    $router->get('/user/{id}', 'UserController@showAllDataUser');

    $router->put('/user/{id}', 'UserController@update');
    $router->delete('/user/{id}', 'UserController@delete');
    //node
    $router->post('/node', 'NodeController@create');
    $router->get('/node', 'NodeController@showAll');
    $router->get('/node/{id}', 'NodeController@showDetailData');
    $router->put('/node/{id}', 'NodeController@update');
    $router->delete('/node/{id}', 'NodeController@delete');
    //hardware
    $router->post('/hardware', 'HardwareController@create');
    $router->get('/hardware', 'HardwareController@showAll');
    $router->get('/hardware/{id}', 'HardwareController@showDetailData');
    $router->put('/hardware/{id}', 'HardwareController@update');
    $router->delete('/hardware/{id}', 'HardwareController@delete');
    //sensor
    $router->post('/sensor', 'SensorController@create');
    $router->get('/sensor', 'SensorController@showAll');
    $router->get('/sensor/{id}', 'SensorController@showDetailData');
    $router->put('/sensor/{id}', 'SensorController@update');
    $router->delete('/sensor/{id}', 'SensorController@delete');
    //channel
    $router->post('/channel', 'ChannelController@create');
});

