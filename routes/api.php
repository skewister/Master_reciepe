<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Autres routes nécessitant l'authentification...
});


Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index'])->name('tags.index');
    Route::get('/{tag}', [TagController::class, 'show'])->name('tags.show');
    Route::delete('/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
});

Route::prefix('/recipes')->group(function () {
    Route::get('/', [RecipeController::class, 'index'])->name('recipes.index');
    Route::post('/', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
    Route::put('/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
    Route::post('/{recipe}/steps', [RecipeController::class, 'addStep']);
    Route::get('/{recipe}/steps', [RecipeController::class, 'listSteps']);
    Route::put('/{recipe}/steps/{step}', [RecipeController::class, 'updateStep']);
    Route::delete('/{recipe}/steps/{step}', [RecipeController::class, 'deleteStep']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/register', [AuthController::class, 'register'])->name('register');


