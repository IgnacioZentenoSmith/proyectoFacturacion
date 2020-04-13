<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();
Route::get('/home', 'HomeController@index')->name('home.index');
Route::get('/', 'HomeController@index')->name('home.index');


Route::get('/contracts', 'ContractsController@index')->name('contracts.index')->middleware('auth');
Route::get('/billings', 'BillingsController@index')->name('billings.index')->middleware('auth');
Route::get('/clients', 'ClientsController@index')->name('clients.index')->middleware('auth');
// USERS ROUTES (RESOURCE CRUD)
Route::resource('users', 'UsersController')->middleware('auth');
// ADMIN ROUTES
Route::get('/admin/roles', 'AdminController@roles')->name('admin.roles')->middleware('auth');
Route::get('/admin', 'AdminController@index')->name('admin.index')->middleware('auth');
// ADMIN AJAX ROUTES
Route::post('/admin/ajax/userUpdate', 'AdminController@ajaxUpdateUserActions')->middleware('auth');
Route::post('/admin/ajax/roleUpdate', 'AdminController@ajaxUpdateRoleActions')->middleware('auth');

