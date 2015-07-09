<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function(){
	return Redirect::to('home');
});

Route::group(array('before' => 'auth'),function(){
	Route::get('admin/files/{id}', 'AdminController@files');
	Route::get('admin/users/{id}/delete', 'AdminController@delete');
	Route::resource('admin', 'AdminController');
	Route::get('home/files', 'HomeController@getFiles');
	Route::get('home/edit/{key}/{method}', 'HomeController@edit');
	Route::get('home/edit/{key}/setactive/{id}', 'HomeController@setActive');
	Route::post('home/edit/{key}/revision', 'HomeController@doRevision');
	Route::put('home/edit/', 'HomeController');
	Route::delete('home/edit', 'HomeController');
});

Route::controller('login/password', 'RemindersController');

Route::get('notfound',function(){
	return View::make('notfound');
});

Route::get('home/do_download/{key}','HomeController@doDownload');
Route::get('home/success/{key}','HomeController@getSuccess');

Route::controller('home','HomeController');
Route::controller('login','LoginController');

Route::post('login/{param}','LoginController@test');