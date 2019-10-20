<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/login', 'LoginController@index');
$router->post('/login/logout', 'LoginController@logout');

$router->get('/nilai', 'NilaiController@index');
$router->post('/nilai', 'NilaiController@store');
$router->delete('/nilai/{id}', 'NilaiController@delete');
$router->patch('/nilai/{id}', 'NilaiController@update');
$router->get('/nilai/{id}', 'NilaiController@get');

$router->get('/jadwal', 'JadwalController@index');
$router->get('/jadwal/{id}', 'JadwalController@get');