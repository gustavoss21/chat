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

Route::controller(ChatController::class)->group(function () {
    route::get('chat','index');
    route::post('chat','store');
    route::patch('chat','updateViews');
});