<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('users','UserController');
 Route::get('users/{id}/edit/','UserController@edit');

 Route::get('/admin_logout', 'Auth\LoginController@logout');


 Route::resource('sample', 'SampleController');

Route::post('sample/update', 'SampleController@update')->name('sample.update');

Route::get('sample/destroy/{id}', 'SampleController@destroy');


 Route::resource('company', 'CompanyController');

Route::post('company/update', 'CompanyController@update')->name('company.update');

Route::get('company/destroy/{id}', 'CompanyController@destroy');

Route::resource('admin', 'AdminController');

Route::post('admin/update', 'AdminController@update')->name('admin.update');

Route::get('admin/destroy/{id}', 'AdminController@destroy');



Route::resource('invoice', 'AdminInvoiceController');

Route::post('invoice/update', 'AdminInvoiceController@update')->name('invoice.update');

Route::get('invoice/destroy/{id}', 'AdminInvoiceController@destroy');

Route::get('invoice/pay/{id}', 'AdminInvoiceController@pay');

// Route::get('pdf', function(){
    
//     $pdf = PDF::loadView('invoice_pdf');
//     return $pdf->download('invoice.pdf');
// });

Route::get('invoice/invoice-pdf/{id}', 'AdminInvoiceController@pdf')->name('admin.invoice_pdf');

Route::get('dashboard', 'AdminController@dashboard')->name('admin.dashboard');

Route::get('destroy_records', 'AdminController@destroyRecords');