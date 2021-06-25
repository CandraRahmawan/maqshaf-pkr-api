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
$router->get('user/{id}', 'UserController@findById');
$router->get('user/{id}/{pin}', 'UserController@findByIdAndPin');
$router->post('user/add', 'UserController@insert');
$router->put('user/update/{id}', 'UserController@updateData');
$router->put('user/updatepin/{id}', 'UserController@updatePin');


$router->get('administrator/all', 'AdministratorController@findAll');
$router->get('administrator/{id}', 'AdministratorController@findById');
$router->post('administrator/add', 'AdministratorController@insert');
$router->post('administrator/login', 'AdministratorController@login');
$router->put('administrator/update/{id}', 'AdministratorController@updateData');
$router->put('administrator/updatepassword/{id}', 'AdministratorController@updatePassword');
$router->delete('administrator/delete/{id}', 'AdministratorController@deleteDataById');


$router->get('mastergood/all', 'MasterGoodController@findAll');
$router->get('mastergood/{id}', 'MasterGoodController@findById');
$router->post('mastergood/add', 'MasterGoodController@insert');
$router->put('mastergood/update/{id}', 'MasterGoodController@updateData');
$router->post('mastergood/upload/image/{id}', 'MasterGoodController@uploadImage');


$router->get('deposit/all', 'DepositController@findAll');
$router->get('deposit/{id}', 'DepositController@findById');
$router->post('deposit/debet/{userId}', 'DepositController@debit');
$router->post('deposit/kredit/{userId}', 'DepositController@kredit');
$router->post('transactions/buy/{userId}', 'DepositController@buyItem');


$router->get('transactions/all', 'TransactionsController@findAll');
$router->get('transactions/{id}', 'TransactionsController@findById');
$router->post('transactions/add', 'TransactionsController@insert');
$router->put('transactions/update/{id}', 'TransactionsController@updateData');
