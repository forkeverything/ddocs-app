<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth');


Route::post('/c/make/email', 'ChecklistsController@postNewChecklistFromEmailWebhook');

// NEW ROUTES THAT HAVEN'T BEEN RENAMED YET

// Checklist - Auth Route
Route::get('/checklists', 'ChecklistsController@getForAuthenticatedUser');
Route::post('/checklists', 'ChecklistsController@postNewChecklist');


// Checklist - Public Route
Route::get('/c/{checklist_hash}', 'ChecklistsController@getSingle');
Route::get('/c/{checklist_hash}/files', 'ChecklistsController@getFilesForChecklist');

// Recipients
//Route::get('/recipients/{recipient_hash}/turn_off_notifications', 'RecipientsController@getTurnOffNotifications');

// File Requests
Route::post('/file_requests/{file_request_hash}/upload', 'FileRequestsController@postUploadFile');
//Route::put('/file_requests/{file_request_hash}', 'FileRequestsController@putModifyRequest');
//Route::post('/file_requests/{file_request_hash}/upload', 'FileRequestsController@postUploadFile');
//Route::post('/file_requests/{file_request_hash}/reject', 'FileRequestsController@postRejectUploadedFile');
//Route::get('/file_requests/{file_request_hash}/history', 'FileRequestsController@getHistory');
Route::get('/file_requests/{file_request_hash}/notes', 'FileRequestsController@getNotes');
//Route::delete('/file_requests/{file_request_hash}', 'FileRequestsController@deleteFiles');
//Route::post('/file_requests/{file_request_hash}/comments', 'FileRequestsController@postAddComment');
//Route::get('/file_requests/user/{user}', 'FileRequestsController@getFileRequestsForUser');

// Notes
Route::post('/note', 'NotesController@postNew');
Route::put('/note/{note_hash}', 'NotesController@putUpdate');
Route::delete('/note/{note_hash}', 'NotesController@delete');

// Files
Route::get('/files', 'FilesController@getForUser');

// Projects
Route::post('/projects', 'ProjectsController@postSaveNew');
Route::put('/projects/{project}', 'ProjectsController@putUpdate');
Route::delete('/projects/{project}', 'ProjectsController@delete');
Route::post('/projects/{project}/folders', 'ProjectsController@postCreateFolder');
Route::put('/projects/{project}/folders/{projectFolder}', 'ProjectsController@putUpdateFolder');
Route::delete('/projects/{project}/folders/{projectFolder}', 'ProjectsController@deleteFolder');
Route::post('/projects/{project}/folders/{projectFolder}/files', 'ProjectsController@postAddFile');
Route::put('/projects/{project}/files/{projectFile}', 'ProjectsController@putUpdateFile');
Route::post('/projects/{project}/files/{projectFile}', 'ProjectsController@postAddComment');

// Comments
Route::get('/comments/project_file/{projectFile}', 'CommentsController@getForProjectFile');
Route::get('/comments/file_request/{file_request_hash}', 'CommentsController@getForFileRequest');

// Account
Route::post('/account/subscription', 'AccountController@postSubscribe');
Route::delete('/account/subscription', 'AccountController@deleteCancelSubscription');
Route::post('/account/subscription/resume', 'AccountController@postResumeSubscription');
Route::post('/account/coupon', 'AccountController@postClaimCoupon');