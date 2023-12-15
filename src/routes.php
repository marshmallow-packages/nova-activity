<?php

use Illuminate\Support\Facades\Route;
use Marshmallow\NovaActivity\Http\Controllers\RunActionController;
use Marshmallow\NovaActivity\Http\Controllers\GetActivityController;
use Marshmallow\NovaActivity\Http\Controllers\SetQuickReplyController;
use Marshmallow\NovaActivity\Http\Controllers\CreateActivityController;

Route::post('/{activity_id}/run-action', RunActionController::class);
Route::post('/{activity_id}/set-quick-reply', SetQuickReplyController::class);
Route::post('/{resourceName}/{resourceId}', CreateActivityController::class);
Route::get('/{resourceName}/{resourceId}/get-activity', GetActivityController::class);
