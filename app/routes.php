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
Route::get('login', function(){
	return Redirect::to(route('users.login'));
});

Route::get('/', array('as' => 'home', function(){
	return Redirect::to('files/upload');}
));

Route::get('files/upload/', array('as' => 'files.create', 'uses' => 'FilesController@create'));
Route::get('files/success/{files}', array('as' => 'files.success', 'uses' => 'FilesController@success'));
Route::get('files/download/{files}', array('as' => 'files.download', 'uses' => 'FilesController@download'));
Route::resource('files', 'FilesController',
					array('only' => array('store', 'show')));

Route::group(array('before' => 'guest'), function(){
	Route::get('users/login/', array('as' => 'users.login', 'uses' => 'UsersController@login'));
	Route::get('users/register/', array('as' => 'users.register', 'uses' => 'UsersController@create'));
	Route::post('users/do-login/', array('as' => 'users.do-login', 'uses' => 'UsersController@doLogin'));
	Route::post('users/register/', array('as' => 'users.store', 'uses' => 'UsersController@store'));
});

Route::group(array('before' => 'auth'), function(){
	Route::put('files/move-folder/{files}', array('as' => 'files.move-folder', 'uses' => 'FilesController@moveFolder'));
	Route::resource('files', 'FilesController',
						array('only' => array('index', 'edit', 'destroy', 'update')));

	Route::resource('folders', 'FoldersController',
						array('only' => array('store', 'destroy', 'edit', 'update')));
	Route::get('folders/{folders?}', array('as' => 'folders.show', 'uses' => 'FoldersController@show'))
		->where('folders', '(.*)');

	Route::get('revisions/set-active/{key}/{id}', array('as' => 'revisions.set-active', 'uses' => 'RevisionsController@setActive'));
	Route::resource('revisions', 'RevisionsController',
						array('only' => array('show', 'update')));

	Route::get('users/edit', array('as' => 'users.edit', 'uses' => 'UsersController@edit'));
	Route::get('users/logout/', array('as' => 'users.logout', 'uses' => 'UsersController@logout'));
	Route::put('users/edit', array('as' => 'users.update', 'uses' => 'UsersController@update'));
});

Route::group(array('prefix' => 'admin', 'before' => 'auth|isAdmin'), function(){
	Route::get('/', array('as' => 'admin.index', 'uses' => 'AdminController@index'));

	Route::resource('files', 'FilesAdminController',
						array('only' => array('index', 'show')));

	Route::resource('folders', 'FoldersAdminController',
						array('only' => array('index')));

	Route::get('users/{id}/delete', array('as' =>'admin.users.delete', 'uses' => 'UsersAdminController@delete'));
	Route::resource('users', 'UsersAdminController',
						array('only' => array('index', 'destroy')));

	Route::resource('logs', 'LogsAdminController',
						array('only' => array('index')));
});

Route::controller('users/forget-password', 'RemindersController');
Route::get('notfound',function(){
	return View::make('notfound');
});
Route::get('unauthorized',function(){
	return View::make('unauthorized');
});
