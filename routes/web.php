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

// Route::get('activate/{token}', 'Auth\RegisterController@activate')
//     ->name('activate');


Route::get('populate', 'MappingController@populate');
Route::get('import', 'ProcessController@import');
Route::get('block', 'ProcessController@blockTransactions');
Route::get('genesis', 'BlocksController@genesis');
Route::get('verify', 'BlocksController@verifyBlock');
Route::any('cron', 'ProcessController@startProcessing');


Route::any('pdf', 'ProcessController@pdfProcess');

Route::group(['middleware' => 'user'], function() {
	Route::get('/upload', 'FileController@index');
	Route::post('/uploadfile', 'FileController@upload');
	Route::get('/sendEmail', 'MailController@mail');

	Route::get('/home', 'BlocksController@index');
	Route::get('/test', 'BlocksController@index');
	Route::post('/test', 'BlocksController@test');
	Route::post('/list', 'BlocksController@list');
});