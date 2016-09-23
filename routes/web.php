<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


Auth::routes();

// Static Pages
Route::get('/', 'PagesController@getLandingMain');

// Checklists
Route::get('/c', 'ChecklistsController@getListsView');
Route::get('/c/get', 'ChecklistsController@getForAuthenticatedUser');
Route::get('/c/make', 'ChecklistsController@getMakeForm');
Route::post('/c/make', 'ChecklistsController@postNewChecklist');
Route::get('/c/{checklist_hash}', 'ChecklistsController@getSingleChecklist');
Route::get('/c/{checklist_hash}/weightings', 'ChecklistsController@getWeightings');
Route::get('/c/{checklist_hash}/files', 'ChecklistsController@getFilesForChecklist');

// Recipients
Route::get('/r/{recipient_hash}/turn_off_notifications', 'RecipientsController@getTurnOffNotifications');

// FileRequests
Route::post('/fr/{file_request_hash}/upload', 'FileRequestsController@postUploadFile');
Route::put('/fr/{file_request_hash}', 'FileRequestsController@putModifyRequest');
Route::post('/fr/{file_request_hash}/upload', 'FileRequestsController@postUploadFile');
Route::post('/fr/{file_request_hash}/reject', 'FileRequestsController@postRejectUploadedFile');
Route::get('/fr/{file_request_hash}/history', 'FileRequestsController@getHistory');
Route::get('/fr/{file_request_hash}/notes', 'FileRequestsController@getNotes');
Route::delete('/fr/{file_request_hash}', 'FileRequestsController@deleteFiles');

// Notes
Route::post('/note', 'NotesController@postNew');
Route::put('/note/{note_hash}', 'NotesController@putUpdate');
Route::delete('/note/{note_hash}', 'NotesController@delete');


// Files
Route::get('/files', 'FilesController@getForUser');

// Projects
Route::get('/projects', 'ProjectsController@getAll');
Route::get('/projects/start', 'ProjectsController@getStartForm');
Route::post('/projects', 'ProjectsController@postSaveNew');
Route::get('/projects/{project}', 'ProjectsController@getSingle');
Route::put('/projects/{project}', 'ProjectsController@putUpdate');
Route::delete('/projects/{project}', 'ProjectsController@delete');
    // Cat & Files
    Route::post('/projects/{project}/categories', 'ProjectsController@postNewCategory');
    Route::post('/projects/{project}/files', 'ProjectsController@postNewFile');
    Route::put('/projects/{project}/item', 'ProjectsController@putUpdateItem');
    Route::put('/projects/{project}/positions', 'ProjectsController@putUpdatePositions');

// Account
Route::get('/account', 'AccountController@getAccountOverview');
Route::post('/account/subscription', 'AccountController@postSubscribe');
Route::delete('/account/subscription', 'AccountController@deleteCancelSubscription');
Route::post('/account/subscription/resume', 'AccountController@postResumeSubscription');
Route::post('/account/coupon', 'AccountController@postClaimCoupon');

Route::get('/test', function () {
    return getProjectItems('App\Project', 1);
    return \App\Project::first()->items();
});
