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
    if(Auth::check())return redirect('/checklist');
    return view('landing');
});

Route::auth();

Route::get('/checklist', 'ChecklistsController@getListsView');
route::get('/checklist/get', 'ChecklistsController@getForAuthenticatedUser');
Route::get('/checklist/make', 'ChecklistsController@getMakeForm');

Route::post('/checklist/make', 'ChecklistsController@postNewChecklist');

Route::post('/checklist/make/email', 'ChecklistsController@postNewChecklistFromEmailWebhook');
Route::get('/checklist/{checklist_hash}', 'ChecklistsController@getSingleChecklist');
Route::get('/checklist/{checklist_hash}/files', 'ChecklistsController@getFilesForChecklist');
Route::post('/checklist/{checklist_hash}/turn_off_notifications', 'ChecklistsController@postTurnOffNotifications');

Route::post('/file/{fileRequest}', 'FilesController@postUploadFile');
Route::post('/file/{fileRequest}/reject', 'FilesController@postRejectUploadedFile');

Route::get('/account', 'AccountsController@getAccountOverview');
Route::post('/account/subscription', 'AccountsController@postSubscribe');
Route::delete('/account/subscription', 'AccountsController@deleteCancelSubscription');
Route::post('/account/subscription/resume', 'AccountsController@postResumeSubscription');
