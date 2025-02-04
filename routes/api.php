<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(ChatController::class)->group(function () {
    Route::get('/chat', 'messages');
    Route::post('/chat', 'sendMessage');
});

Route::get('/users', [ProfileController::class,'contacts']);

Route::apiResource('message',ChatController::class);