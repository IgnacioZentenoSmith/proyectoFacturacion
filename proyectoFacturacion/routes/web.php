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

// Email Verification Routes...
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
Route::put('email/{id}/setPassword', 'Auth\VerificationController@setPassword')->name('email.setPassword')->middleware('auth');

Route::get('/home', 'HomeController@index')->name('home.index');
Route::get('/', 'HomeController@index')->name('home.index');


Route::get('/contracts', 'ContractsController@index')->name('contracts.index')->middleware('auth');
Route::get('/billings', 'BillingsController@index')->name('billings.index')->middleware('auth');


// ADMIN ROUTES (RESOURCE CRUD)
Route::resource('admin', 'AdminController')->middleware('auth');
Route::get('/admin/{id}/editPermisos', 'AdminController@editPermisos')->name('admin.editPermisos')->middleware('auth');
Route::put('/admin/{id}/updatePermisos', 'AdminController@updatePermisos')->name('admin.updatePermisos')->middleware('auth');
Route::post('/admin/{id}', 'AdminController@changeStatus')->name('admin.changeStatus')->middleware('auth');

// CLIENT ROUTES (RESOURCE CRUD)
Route::resource('clients', 'ClientsController')->middleware('auth');

