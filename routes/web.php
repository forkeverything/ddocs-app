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
Route::get('/c/{checklist_hash}/files', 'ChecklistsController@getFilesForChecklist');

// Recipients
Route::get('/r/{recipient_hash}/turn_off_notifications', 'RecipientsController@getTurnOffNotifications');

// FileRequests
Route::post('/fr/{file_request_hash}/upload', 'FileRequestsController@postUploadFile');
Route::put('/fr/{file_request_hash}', 'FileRequestsController@putModifyRequest');
Route::post('/fr/{file_request_hash}/upload', 'FileRequestsController@postUploadFile');
Route::post('/fr/{file_request_hash}/reject', 'FileRequestsController@postRejectUploadedFile');
Route::get('/fr/{file_request_hash}/history', 'FileRequestsController@getHistory');
Route::delete('/fr/{file_request_hash}', 'FileRequestsController@deleteFiles');

// Files
Route::get('/files', 'FilesController@getForUser');


// Account
Route::get('/account', 'AccountController@getAccountOverview');
Route::post('/account/subscription', 'AccountController@postSubscribe');
Route::delete('/account/subscription', 'AccountController@deleteCancelSubscription');
Route::post('/account/subscription/resume', 'AccountController@postResumeSubscription');
Route::post('/account/coupon', 'AccountController@postClaimCoupon');
