<?php

use \Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('roles', 'Admin\RolesController@list');
Route::post('roles', 'Admin\RolesController@create');
Route::put('roles', 'Admin\RolesController@updateMultiple');
Route::delete('roles', 'Admin\RolesController@deleteMultiple');

Route::get('roles/{id}', 'Admin\RolesController@show');
Route::put('roles/{id}', 'Admin\RolesController@update');
Route::delete('roles/{id}', 'Admin\RolesController@delete');


Route::get('users', 'Admin\UsersController@list');
Route::post('users', 'Admin\UsersController@create');
Route::put('users', 'Admin\UsersController@updateMultiple');
Route::delete('users', 'Admin\UsersController@deleteMultiple');

Route::get('users/exists', 'Admin\UsersController@exists');
Route::post('users/exists', 'Admin\UsersController@exists');

Route::get('users/{id}', 'Admin\UsersController@show');
Route::put('users/{id}', 'Admin\UsersController@update');
Route::delete('users/{id}', 'Admin\UsersController@delete');