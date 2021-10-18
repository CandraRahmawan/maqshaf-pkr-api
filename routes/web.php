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

$router->group(['prefix' => null, 'middleware' => 'auth'], function() use ($router) {
	
	$router->get('user/all', 'UserController@findNameOrNis');
	$router->post('user/add', [ 'uses' =>'UserController@insert']);
	$router->put('user/update/{id}', [ 'uses' =>'UserController@updateData']);
	$router->delete('user/delete/{id}', [ 'uses' =>'UserController@deletById']);
	$router->post('user/active/{id}', [ 'uses' =>'UserController@activedById']);


	$router->get('administrator/all', [ 'uses' =>'AdministratorController@findAll']);
	$router->post('administrator/add', [ 'uses' =>'AdministratorController@insert']);
	$router->put('administrator/update/{id}', [ 'uses' =>'AdministratorController@updateData']);
	$router->put('administrator/update-password/{id}', [ 'uses' =>'AdministratorController@updatePassword']);
	$router->delete('administrator/delete/{id}', [ 'uses' =>'AdministratorController@deleteDataById']);
	$router->get('administrator/{id}', [ 'uses' =>'AdministratorController@findById']);


	
	$router->get('deposit/all', [ 'uses' => 'DepositController@findAllKreditByTrxCode']);
	$router->post('deposit/kredit/{userId}', 'DepositController@kredit');


	
	$router->get('debet/all', [ 'uses' => 'DepositController@findAllDebetNisOrTransactionCode']);
	

	$router->get('mastergood/all', 'MasterGoodController@findAll');
	$router->post('mastergood/add', [ 'uses' =>'MasterGoodController@insert']);
	$router->post('mastergood/update/{id}', [ 'uses' =>'MasterGoodController@updateData']);


	$router->get('transactions/all', [ 'uses' =>'TransactionsController@findAll']);

	$router->post('withDrawl/{id}', [ 'uses' =>'DepositController@withDrawl']);

	$router->get('transactions/dashboard', [ 'uses' =>'TransactionsController@dashboard']);

});


$router->get('user/saldo', 'UserController@findByNis');


$router->put('user/update-pin/{id}', 'UserController@updatePin');
$router->put('user/reset-pin/{id}', 'UserController@resetPin');
$router->get('user/saldo/{id}', 'UserController@userSaldo');
$router->get('user/{id}/{pin}', 'UserController@findByIdAndPin');
$router->get('user/{id}', 'UserController@findById');


$router->post('administrator/login', 'AdministratorController@login');
$router->post('administrator/logout', 'AdministratorController@logout');


$router->get('mastergood/search', 'MasterGoodController@findByName');
$router->post('mastergood/upload/image/{id}', 'MasterGoodController@uploadImage');
$router->get('mastergood/image/{id}', 'MasterGoodController@getImage');
$router->get('mastergood/{id}', 'MasterGoodController@findById');


$router->post('deposit/debet/{userId}', 'DepositController@debet');
$router->post('transactions/buy/{userId}', 'DepositController@buyItem');
$router->get('deposit/{id}', 'DepositController@findById');


$router->post('transactions/add', 'TransactionsController@insert');
$router->put('transactions/update/{id}', 'TransactionsController@updateData');
$router->get('transactions/{id}', 'TransactionsController@findById');


