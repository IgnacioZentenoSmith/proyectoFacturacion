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
Route::get('/home/gci', 'HomeController@GCI_Api')->name('home.gci');
Route::get('/home/pvi', 'HomeController@PVI_Api')->name('home.pvi');
Route::get('/home/etdtp', 'HomeController@ETDTP_Api')->name('home.etdtp');
Route::get('/home/licita', 'HomeController@LICITA_Api')->name('home.licita');

Route::get('/', 'HomeController@index')->name('home.index');


Route::get('/contracts', 'ContractsController@index')->name('contracts.index')->middleware('auth');


// ADMIN ROUTES (RESOURCE CRUD)
Route::resource('admin', 'AdminController')->middleware('auth');
Route::get('/admin/{id}/editPermisos', 'AdminController@editPermisos')->name('admin.editPermisos')->middleware('auth');
Route::put('/admin/{id}/updatePermisos', 'AdminController@updatePermisos')->name('admin.updatePermisos')->middleware('auth');
Route::post('/admin/{id}', 'AdminController@changeStatus')->name('admin.changeStatus')->middleware('auth');
Route::post('/admin/{id}/resend', 'AdminController@resendVerification')->name('admin.resendVerification')->middleware('auth');

// CLIENT ROUTES (RESOURCE CRUD)
Route::resource('clients', 'ClientsController')->middleware('auth');
//hijos
Route::get('/clients/{idCliente}/childrenIndex', 'ClientsController@childrenIndex')->name('clients.childrenIndex')->middleware('auth');
Route::get('/clients/{idCliente}/childrenCreate', 'ClientsController@childrenCreate')->name('clients.childrenCreate')->middleware('auth');
Route::post('/clients/{idCliente}/childrenStore', 'ClientsController@childrenStore')->name('clients.childrenStore')->middleware('auth');
Route::get('/clients/{idCliente}/childrenEdit/{idHijo}', 'ClientsController@childrenEdit')->name('clients.childrenEdit')->middleware('auth');
Route::put('/clients/{idCliente}/childrenUpdate/{idHijo}', 'ClientsController@childrenUpdate')->name('clients.childrenUpdate')->middleware('auth');
Route::delete('/clients/{idCliente}/childrenDestroy/{idHijo}', 'ClientsController@childrenDestroy')->name('clients.childrenDestroy')->middleware('auth');


// PARAMETERIZATION ROUTES
Route::get('/parameterization', 'ParameterizationController@index')->name('parameterization.index')->middleware('auth');
// CRUD MODULES
Route::get('/parameterization/modules', 'ParameterizationController@modulesIndex')->name('parameterization.modules')->middleware('auth');
Route::get('/parameterization/modulesCreate', 'ParameterizationController@modulesCreate')->name('parameterization.modulesCreate')->middleware('auth');
Route::post('/parameterization/modules', 'ParameterizationController@modulesStore')->name('parameterization.modulesStore')->middleware('auth');
Route::get('/parameterization/{id}/modulesEdit', 'ParameterizationController@modulesEdit')->name('parameterization.modulesEdit')->middleware('auth');
Route::put('/parameterization/{id}/modulesUpdate', 'ParameterizationController@modulesUpdate')->name('parameterization.modulesUpdate')->middleware('auth');
Route::delete('/parameterization/{id}/destroy', 'ParameterizationController@modulesDestroy')->name('parameterization.modulesDestroy')->middleware('auth');
// CRUD PAYMENTUNITS
Route::get('/parameterization/paymentunits', 'ParameterizationController@paymentunitsIndex')->name('parameterization.paymentunits')->middleware('auth');
Route::get('/parameterization/paymentunitsCreate', 'ParameterizationController@paymentunitsCreate')->name('parameterization.paymentunitsCreate')->middleware('auth');
Route::post('/parameterization/paymentunits', 'ParameterizationController@paymentunitsStore')->name('parameterization.paymentunitsStore')->middleware('auth');
Route::get('/parameterization/{id}/paymentunitsEdit', 'ParameterizationController@paymentunitsEdit')->name('parameterization.paymentunitsEdit')->middleware('auth');
Route::put('/parameterization/{id}/paymentunitsUpdate', 'ParameterizationController@paymentunitsUpdate')->name('parameterization.paymentunitsUpdate')->middleware('auth');
Route::delete('/parameterization/{id}/paymentunits', 'ParameterizationController@paymentunitsDestroy')->name('parameterization.paymentunitsDestroy')->middleware('auth');
//CRUD CONTRACTS
Route::resource('contracts', 'ContractsController')->middleware('auth');
Route::get('/contracts/{id}/editContractStatus', 'ContractsController@editContractStatus')->name('contracts.editContractStatus')->middleware('auth');
//CRUD CONDICIONES CONTRACTUALES
Route::get('/contracts/{id}/conditions', 'ContractsController@conditionsIndex')->name('contracts.conditions')->middleware('auth');
Route::get('/contracts/{id}/conditionsCreate', 'ContractsController@conditionsCreate')->name('contracts.conditionsCreate')->middleware('auth');
Route::post('/contracts/{id}/conditions', 'ContractsController@conditionsStore')->name('contracts.conditionsStore')->middleware('auth');
Route::get('/contracts/{id}/conditionsEdit', 'ContractsController@conditionsEdit')->name('contracts.conditionsEdit')->middleware('auth');
Route::put('/contracts/{id}/conditionsUpdate', 'ContractsController@conditionsUpdate')->name('contracts.conditionsUpdate')->middleware('auth');
Route::delete('/contracts/{id}/conditions', 'ContractsController@conditionsDestroy')->name('contracts.conditionsDestroy')->middleware('auth');
//CRUD CANTIDADES DE CONDICIONES CONTRACTUALES
Route::get('/contracts/{idContrato}/quantities/{periodo}', 'ContractsController@quantitiesIndex')->name('contracts.quantities')->middleware('auth');
Route::put('/contracts/{idCantidad}/quantitiesUpdate/{periodo}', 'ContractsController@quantitiesUpdate')->name('contracts.quantitiesUpdate')->middleware('auth');
//RUTAS DE DISTRIBUCIONES DE CONTRATO
Route::get('/contracts/{idContrato}/distributions', 'ContractdistributionController@distributionsIndex')->name('contracts.distributions')->middleware('auth');
Route::put('/contracts/{idContrato}/distributions', 'ContractdistributionController@distributionsUpdate')->name('contracts.distributionsUpdate')->middleware('auth');

Route::get('/billings/{periodo}', 'TributarydocumentsController@index')->name('billings.index')->middleware('auth');
Route::post('/billings/{periodo}/generateDocuments', 'TributarydocumentsController@generateDocumentos')->name('billings.generateDocuments')->middleware('auth');
Route::delete('/billings/{id}', 'TributarydocumentsController@documentDestroy')->name('billings.documentDestroy')->middleware('auth');
//Route::post('/billings/{id}/generateNotaCredito/{periodo}', 'TributarydocumentsController@generateNotaCredito')->name('billings.generateNotaCredito')->middleware('auth');

Route::get('/billings/{idTributarydocument}/paymentDetails', 'TributarydocumentsController@paymentDetailsIndex')->name('billings.paymentDetails')->middleware('auth');

Route::get('/billings/{idTributarydocument}/redistribute', 'TributarydocumentsController@redistribute')->name('billings.redistribute')->middleware('auth');
Route::put('/billings/{idTributarydocument}/generateRedistribucion', 'TributarydocumentsController@generateRedistribucion')->name('billings.generateRedistribucion')->middleware('auth');
