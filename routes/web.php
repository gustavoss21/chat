<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use APP\Models\User;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('Dashboard', [
        'user' => Auth::user()
    ]);
})->middleware('auth');

Route::get('/list-contact', function(){
    return Inertia::render('listContact', [
        'user' => Auth::user()
    ]);
})->middleware('auth');

Route::get("/chat", function(Request $request){
    $user_received = User::find($request->get('received'));
    
    return Inertia::render('chat', [
        'user' => Auth::user(),
        'user_received'=>$user_received,
        'token'=>$request->session()->token()
    ]);
})->middleware('auth');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
