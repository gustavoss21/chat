<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/users', [ProfileController::class,'contacts']);

Route::controller(ChatController::class)->middleware(App\Http\Middleware\CustomSanctumAuth::class)->group(function () {
    Route::get('/chat','index');
    Route::post('/chat','store');
    Route::patch('/chat','updateViews');
    Route::post('/active-call','activeCall');
    Route::post('/user-conect','userConect');
    Route::get('/active-main-call','activeMainCall');
    Route::get('/call-users','callUsers');
    Route::post('/user-connect','userConnect');
    Route::post('/status-user',[ChatController::class,'statusUser']);
});

