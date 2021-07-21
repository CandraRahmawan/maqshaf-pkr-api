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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('user/all', 'UserController@findAll');
$router->post('user/add', 'UserController@insert');
$router->put('user/update/{id}', 'UserController@updateData');
$router->put('user/update-pin/{id}', 'UserController@updatePin');
$router->get('user/{id}/{pin}', 'UserController@findByIdAndPin');
$router->get('user/{id}', 'UserController@findById');


$router->get('administrator/all', 'AdministratorController@findAll');
$router->post('administrator/add', 'AdministratorController@insert');
$router->post('administrator/login', 'AdministratorController@login');
$router->put('administrator/update/{id}', 'AdministratorController@updateData');
$router->put('administrator/update-password/{id}', 'AdministratorController@updatePassword');
$router->delete('administrator/delete/{id}', 'AdministratorController@deleteDataById');
$router->get('administrator/{id}', 'AdministratorController@findById');

$router->get('mastergood/all', 'MasterGoodController@findAll');
$router->get('mastergood/search', 'MasterGoodController@findByName');
$router->post('mastergood/add', 'MasterGoodController@insert');
$router->post('mastergood/upload/image/{id}', 'MasterGoodController@uploadImage');
$router->get('mastergood/image/{id}', 'MasterGoodController@getImage');
$router->post('mastergood/update/{id}', 'MasterGoodController@updateData');
$router->get('mastergood/{id}', 'MasterGoodController@findById');


$router->get('deposit/all', 'DepositController@findAll');
$router->post('deposit/debet/{userId}', 'DepositController@debit');
$router->post('deposit/kredit/{userId}', 'DepositController@kredit');
$router->post('transactions/buy/{userId}', 'DepositController@buyItem');
$router->get('deposit/{id}', 'DepositController@findById');


$router->get('transactions/all', 'TransactionsController@findAll');
$router->post('transactions/add', 'TransactionsController@insert');
$router->put('transactions/update/{id}', 'TransactionsController@updateData');
$router->get('transactions/{id}', 'TransactionsController@findById');


