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


//Auth::routes();

Route::post('/login', 'Auth\LoginController@login');
Route::post('/refresh_token', 'Auth\Logincontroller@refreshToken');
Route::post('/logout', 'Auth\LoginController@logout');
Route::post('/register', 'Auth\RegisterController@register');

// Static Pages
Route::get('{slug}', function () {
    return view('main');
})->where('slug', '^(?!api/?).*');

// Checklists
//Route::get('/c', 'ChecklistsController@getListsView');


//Route::get('/c/get', 'ChecklistsController@getForAuthenticatedUser');
//Route::get('/c/make', 'ChecklistsController@getMakeForm');
//Route::post('/c/make', 'ChecklistsController@postNewChecklist');

//Route::get('/c/{checklist_hash}/{checklist_name?}', 'ChecklistsController@getSingleChecklist');

// Recipients
//Route::get('/r/{recipient_hash}/turn_off_notifications', 'RecipientsController@getTurnOffNotifications');

// FileRequests
//Route::post('/fr/{file_request_hash}/upload', 'FileRequestsController@postUploadFile');
//Route::put('/fr/{file_request_hash}', 'FileRequestsController@putModifyRequest');
//Route::post('/fr/{file_request_hash}/upload', 'FileRequestsController@postUploadFile');
//Route::post('/fr/{file_request_hash}/reject', 'FileRequestsController@postRejectUploadedFile');
//Route::get('/fr/{file_request_hash}/history', 'FileRequestsController@getHistory');
//Route::get('/fr/{file_request_hash}/notes', 'FileRequestsController@getNotes');
//Route::delete('/fr/{file_request_hash}', 'FileRequestsController@deleteFiles');
//Route::post('/fr/{file_request_hash}/comments', 'FileRequestsController@postAddComment');
//Route::get('/fr/user/{user}', 'FileRequestsController@getFileRequestsForUser');

// Notes
//Route::post('/note', 'NotesController@postNew');
//Route::put('/note/{note_hash}', 'NotesController@putUpdate');
//Route::delete('/note/{note_hash}', 'NotesController@delete');

// Files
//Route::get('/files', 'FilesController@getForUser');

// Project
//Route::get('/projects', 'ProjectsController@getAll');
//Route::post('/projects', 'ProjectsController@postSaveNew');
//Route::get('/projects/{project}', 'ProjectsController@getProject');
//Route::put('/projects/{project}', 'ProjectsController@putUpdate');
//Route::delete('/projects/{project}', 'ProjectsController@delete');
//Route::post('/projects/{project}/folders', 'ProjectsController@postCreateFolder');
//Route::put('/projects/{project}/folders/{projectFolder}', 'ProjectsController@putUpdateFolder');
//Route::delete('/projects/{project}/folders/{projectFolder}', 'ProjectsController@deleteFolder');
//Route::post('/projects/{project}/folders/{projectFolder}/files', 'ProjectsController@postAddFile');
//Route::put('/projects/{project}/files/{projectFile}', 'ProjectsController@putUpdateFile');
//Route::post('/projects/{project}/files/{projectFile}', 'ProjectsController@postAddComment');

// Comments
//Route::get('/comments/project_file/{projectFile}', 'CommentsController@getForProjectFile');
//Route::get('/comments/file_request/{file_request_hash}', 'CommentsController@getForFileRequest');



// Account
//Route::get('/account', 'AccountController@getAccountOverview');
//Route::post('/account/subscription', 'AccountController@postSubscribe');
//Route::delete('/account/subscription', 'AccountController@deleteCancelSubscription');
//Route::post('/account/subscription/resume', 'AccountController@postResumeSubscription');
//Route::post('/account/coupon', 'AccountController@postClaimCoupon');
