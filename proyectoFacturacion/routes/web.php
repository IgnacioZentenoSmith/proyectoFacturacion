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

Route::get('/', 'HomeController@index')->name('home.index');
Route::get('/admin', 'AdminController@index')->name('admin.index')->middleware('auth');
Route::get('/users', 'UsersController@index')->name('users.index')->middleware('auth');
Route::get('/contracts', 'ContractsController@index')->name('contracts.index')->middleware('auth');
Route::get('/billings', 'BillingsController@index')->name('billings.index')->middleware('auth');
Route::get('/clients', 'ClientsController@index')->name('clients.index')->middleware('auth');