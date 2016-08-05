<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');

Route::get('/checklist/make', 'ChecklistsController@getMakeForm');
Route::post('/checklist/make', 'ChecklistsController@postNewChecklist');
Route::get('/checklist/{checklist_hash}', 'ChecklistsController@getSingleChecklist');
Route::get('/checklist/{checklist_hash}/files', 'ChecklistsController@getFilesForChecklist');
Route::post('/checklist/{checklist_hash}/file/{file}', 'ChecklistsController@postUploadFile');