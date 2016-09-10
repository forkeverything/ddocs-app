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
Route::get('/checklist', 'ChecklistsController@getListsView');
Route::get('/checklist/get', 'ChecklistsController@getForAuthenticatedUser');
Route::get('/checklist/make', 'ChecklistsController@getMakeForm');
Route::post('/checklist/make', 'ChecklistsController@postNewChecklist');
Route::get('/checklist/{checklist_hash}', 'ChecklistsController@getSingleChecklist');
Route::get('/checklist/{checklist_hash}/files', 'ChecklistsController@getFilesForChecklist');

// Recipients
Route::get('/recipients/{recipient_hash}/turn_off_notifications', 'RecipientsController@getTurnOffNotifications');

// Files
Route::put('/file/{file_request}', 'FilesController@putModifyRequest');
Route::post('/file/{file_request_hash}/upload', 'FilesController@postUploadFile');
Route::post('/file/{file_request_hash}/reject', 'FilesController@postRejectUploadedFile');
Route::get('/file/{file_request_hash}/history', 'FilesController@getHistory');
Route::delete('/file/{file_request_hash}', 'FilesController@deleteFiles');

// Account
Route::get('/account', 'AccountController@getAccountOverview');
Route::post('/account/subscription', 'AccountController@postSubscribe');
Route::delete('/account/subscription', 'AccountController@deleteCancelSubscription');
Route::post('/account/subscription/resume', 'AccountController@postResumeSubscription');
Route::post('/account/coupon', 'AccountController@postClaimCoupon');
