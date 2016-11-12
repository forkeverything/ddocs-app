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

// Authenticated Routes
Route::group(['middleware' => 'jwt.auth'], function () {

    // Authentication
    Route::get('/auth_user', 'Auth\LoginController@getAuthenticatedUser');

    // Checklist
    Route::get('/checklists', 'ChecklistsController@getForAuthenticatedUser');
    Route::get('/checklists/recent', 'ChecklistsController@getRecent');
    Route::post('/checklists', 'ChecklistsController@postNewChecklist');
    Route::put('/c/{checklist_hash}/recipients', 'ChecklistsController@putUpdateRecipients');
    Route::post('/c/{checklist_hash}/files', 'ChecklistsController@postAddFile');
    Route::delete('/c/{checklist_hash}', 'ChecklistsController@delete');

    // File Requests
    Route::get('/file_requests/user', 'FileRequestsController@getForUser');
    Route::put('/file_requests/{file_request_hash}', 'FileRequestsController@putModifyRequest');
    Route::get('/file_requests/{file_request_hash}/uploads', 'FileRequestsController@getUploads');
    Route::post('/file_requests/{file_request_hash}/reject', 'FileRequestsController@postRejectUploadedFile');
    Route::delete('/file_requests/{file_request_hash}', 'FileRequestsController@deleteFiles');

    // Files
    Route::get('/files', 'FilesController@getForUser');

    // Projects
        // Main
        Route::get('/projects', 'ProjectsController@getUserProjects');
        Route::post('/projects', 'ProjectsController@postSaveNew');
        // Single
        Route::get('/projects/{project}', 'ProjectsController@getSingleProject');
        Route::post('/projects/{project}/invite', 'ProjectsController@postSendInvitation');
        Route::post('/projects/{project}/join', 'ProjectsController@postJoin');
        Route::post('/projects/{project}/manager', 'ProjectsController@postDefineManager');
        Route::delete('/projects/{project}/members/{user}', 'ProjectsController@deleteRemoveMember');
        Route::put('/projects/{project}', 'ProjectsController@putUpdateItems');
        Route::delete('/projects/{project}', 'ProjectsController@delete');
            // Folders
            Route::post('/projects/{project}/folders', 'ProjectsController@postCreateFolder');
            Route::delete('/projects/{project}/folders/{projectFolder}', 'ProjectsController@deleteFolder');
            // Files
            Route::post('/projects/{project}/folders/{projectFolder}/files', 'ProjectsController@postAddFile');
            Route::get('/projects/{project}/files/{projectFile}', 'ProjectsController@getProjectFile');
            Route::post('/projects/{project}/files/{projectFile}/attach_fr', 'ProjectsController@postAttachFileRequest');
            Route::post('/projects/{project}/files/{projectFile}/upload', 'ProjectsController@postUploadFile');
            Route::post('/projects/{project}/files/{projectFile}/assign', 'ProjectsController@postSetProjectFileMemberAssignment');
            Route::delete('/projects/{project}/files/{projectFile}', 'ProjectsController@deleteProjectFile');

    // Comments
    Route::get('/comments/project_file/{projectFile}', 'CommentsController@getProjectFile');
    Route::post('/comments/project_file/{projectFile}', 'CommentsController@postNewProjectFile');
    Route::post('/comments/file_request/{file_request_hash}', 'CommentsController@postNewFileRequest');

    // Account
    Route::post('/account/password', 'AccountController@postChangePassword');

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

    // Comments
    Route::get('/comments/file_request/{file_request_hash}', 'CommentsController@getFileRequest');


// NEW ROUTES THAT HAVEN'T BEEN RENAMED YET

Route::post('/c/make/email', 'ChecklistsController@postNewChecklistFromEmailWebhook');


// Recipients
//Route::get('/recipients/{recipient_hash}/turn_off_notifications', 'RecipientsController@getTurnOffNotifications');





// Account
Route::post('/account/subscription', 'AccountController@postSubscribe');
Route::delete('/account/subscription', 'AccountController@deleteCancelSubscription');
Route::post('/account/subscription/resume', 'AccountController@postResumeSubscription');
Route::post('/account/coupon', 'AccountController@postClaimCoupon');