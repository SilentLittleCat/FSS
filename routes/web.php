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

// Route::get('/', function () {
//     return view('welcome');
// });
// Auth::routes();

App::setLocale('zh');

// Auth
Route::get('/', 'Auth\LoginController@showLoginForm')->name('index');
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Home
Route::get('/home', 'HomeController@index')->name('home');

// Test
Route::get('/test', 'TestController@index');

Route::group(['middleware' => 'auth'], function() {

	// Project
	Route::get('projects', 'ProjectController@index')->name('projects.index');
	Route::get('projects/create', 'ProjectController@create')->middleware('isadmin')->name('projects.create');
	Route::get('projects/edit', 'ProjectController@edit')->middleware('can-manage-project')->name('projects.edit');
	Route::get('projects/{id}', 'ProjectController@show')->name('projects.show');

	// Member
	Route::get('members/manage', 'MemberController@manage')->middleware('can-manage-project')->name('members.manage');
	Route::get('projects/{id}/members/{user_id}/permission', 'MemberController@permission')->middleware('isadmin')->name('projects.members.permission');
	Route::post('members/synchronize', 'MemberController@synchronize')->name('members.synchronize');

	// Version
	Route::get('versions/create', 'VersionController@create')->middleware('can-manage-project')->name('versions.create');
	Route::get('versions/manage', 'VersionController@manage')->middleware('can-manage-project')->name('versions.manage');
	Route::get('versions/edit', 'VersionController@edit')->middleware('can-manage-project')->name('versions.edit');
	Route::post('projects/{id}/versions/{version_id}/uploadfiles', 'VersionController@uploadfiles')->name('projects.versions.uploadfiles');
	Route::post('projects/{id}/versions/{version_id}/url-upload', 'VersionController@urlUpload')->name('projects.versions.url-upload');

	// File
	Route::get('files/multi-download', 'FileController@multiDownload')->name('files.multi-download');
	Route::get('files/{id}/download', 'FileController@download')->name('files.download');
	Route::post('files/multi-delete', 'FileController@multiDestroy')->name('files.multi-delete');
	Route::put('files/{id}', 'FileController@update')->name('files.update');
	Route::delete('files/{id}', 'FileController@destroy')->name('files.destroy');

	//Checklist
	Route::get('checklists', 'ChecklistController@index')->name('checklists.index');
	Route::get('checklists/template/create', 'ChecklistController@createTemplate')->name('checklists.template.create');
	Route::delete('checklists/template/{id}', 'ChecklistController@deleteTemplate')->name('checklists.template.delete');
	Route::get('checklists/create', 'ChecklistController@create')->name('checklists.create');
	Route::get('checklists/{id}/export', 'ChecklistController@export')->name('checklists.export');
	Route::get('checklists/{id}/edit', 'ChecklistController@edit')->name('checklists.edit');
	Route::get('checklists/{id}/filter', 'ChecklistController@filter')->name('checklists.filter');
	Route::put('checklists/{id}', 'ChecklistController@update')->name('checklists.update');
	Route::post('checklists', 'ChecklistController@store')->name('checklists.store');

	// Project
	Route::post('projects', 'ProjectController@store')->middleware('isadmin')->name('projects.store');
	Route::put('projects/{id}', 'ProjectController@update')->name('projects.update');
	Route::delete('projects/{id}', 'ProjectController@destroy')->name('projects.destroy');

	// Member
	Route::post('projects/{id}/members', 'MemberController@store')->name('projects.members.store');
	Route::delete('projects/{id}/members/{user_id}', 'MemberController@destroy')->name('projects.members.destroy');

	// Version
	Route::post('projects/{id}/versions', 'VersionController@store')->name('projects.versions.store');
	Route::put('projects/{id}/versions/{version_id}', 'VersionController@update')->name('projects.versions.update');
	Route::delete('projects/{id}/versions/{version_id}', 'VersionController@destroy')->name('projects.versions.destroy');
});
