<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\GoogleController;

Route::get('/login/google', [GoogleController::class, 'redirectToGoogle'])->name('login');
Route::get('/login/google/callback', [GoogleController::class, 'handleGoogleCallback']);



