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

$router->group([
    'prefix' => 'api/v1',
], function () use ($router) {
    $router->post('/users', 'UsersController@create');
    $router->post('/login', 'UsersController@authenticate');

    //restricted route
    $router->group(['middleware'=>'auth:api'],function () use ($router){
        $router->get('/users', 'UsersController@index');
        $router->get('/me','UsersController@me');
    });
});

