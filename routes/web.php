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

// Allow Anonymous
$app->get('/', 'DefaultController@index');

// Authentication
$app->post('/auth/login', 'AuthController@login');
$app->post('/auth/forgot', 'AuthController@forgot');
$app->post('/auth/register/user', 'AuthController@userRegistration');

// Needs Authorization
$app->get('/auth/user', ['middleware' => ['auth'], 'uses' => 'AuthController@user']);
$app->post('/auth/logout', ['middleware' => ['auth'], 'uses' => 'AuthController@logout']);

// User Management
$app->get('/users', ['middleware' => ['auth'], 'uses' => 'UserController@all']);
$app->get('/users/{id}', ['middleware' => ['auth'], 'uses' => 'UserController@read']);
$app->post('/users', ['middleware' => ['auth'], 'uses' => 'UserController@create']);
$app->post('/users/{id}', ['middleware' => ['auth'], 'uses' => 'UserController@update']);
$app->delete('/users/{id}', ['middleware' => ['auth'], 'uses' => 'UserController@delete']);
