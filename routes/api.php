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
})->middleware('jwt.auth');

// Authenticated Routes
Route::group(['middleware' => 'jwt.auth'], function () {

    // Checklist
    Route::get('/checklists', 'ChecklistsController@getForAuthenticatedUser');
    Route::post('/checklists', 'ChecklistsController@postNewChecklist');

    // Files
    Route::get('/files', 'FilesController@getForUser');

    // Projects
    Route::get('/projects', 'ProjectsController@getUserProjects');
    Route::post('/projects', 'ProjectsController@postSaveNew');
    Route::get('/projects/{project}', 'ProjectsController@getSingleProject');
    Route::delete('/projects/{project}', 'ProjectsController@delete');
    Route::post('/projects/{project}/folders', 'ProjectsController@postCreateFolder');
    Route::delete('/projects/{project}/folders/{projectFolder}', 'ProjectsController@deleteFolder');
    Route::post('/projects/{project}/folders/{projectFolder}/files', 'ProjectsController@postAddFile');
    Route::put('/projects/{project}', 'ProjectsController@putUpdateItems');
    Route::put('/projects/{project}/folders/{projectFolder}', 'ProjectsController@putUpdateFolder');
    Route::put('/projects/{project}/files/{projectFile}', 'ProjectsController@putUpdateFile');
    Route::post('/projects/{project}/files/{projectFile}', 'ProjectsController@postAddComment');
});

// Public Routes

    // Checklist
    Route::get('/c/{checklist_hash}', 'ChecklistsController@getSingle');
    Route::get('/c/{checklist_hash}/files', 'ChecklistsController@getFilesForChecklist');

    // FileRequests
    Route::post('/file_requests/{file_request_hash}/upload', 'FileRequestsController@postUploadFile');
    Route::get('/file_requests/{file_request_hash}/notes', 'FileRequestsController@getNotes');

    // Notes
    Route::post('/note', 'NotesController@postNew');
    Route::put('/note/{note_hash}', 'NotesController@putUpdate');
    Route::delete('/note/{note_hash}', 'NotesController@delete');


// NEW ROUTES THAT HAVEN'T BEEN RENAMED YET

Route::post('/c/make/email', 'ChecklistsController@postNewChecklistFromEmailWebhook');


// Recipients
//Route::get('/recipients/{recipient_hash}/turn_off_notifications', 'RecipientsController@getTurnOffNotifications');

// File Requests
//Route::put('/file_requests/{file_request_hash}', 'FileRequestsController@putModifyRequest');
//Route::post('/file_requests/{file_request_hash}/upload', 'FileRequestsController@postUploadFile');
//Route::post('/file_requests/{file_request_hash}/reject', 'FileRequestsController@postRejectUploadedFile');
//Route::get('/file_requests/{file_request_hash}/history', 'FileRequestsController@getHistory');
//Route::delete('/file_requests/{file_request_hash}', 'FileRequestsController@deleteFiles');
//Route::post('/file_requests/{file_request_hash}/comments', 'FileRequestsController@postAddComment');
//Route::get('/file_requests/user/{user}', 'FileRequestsController@getFileRequestsForUser');



// Comments
Route::get('/comments/project_file/{projectFile}', 'CommentsController@getForProjectFile');
Route::get('/comments/file_request/{file_request_hash}', 'CommentsController@getForFileRequest');

// Account
Route::post('/account/subscription', 'AccountController@postSubscribe');
Route::delete('/account/subscription', 'AccountController@deleteCancelSubscription');
Route::post('/account/subscription/resume', 'AccountController@postResumeSubscription');
Route::post('/account/coupon', 'AccountController@postClaimCoupon');