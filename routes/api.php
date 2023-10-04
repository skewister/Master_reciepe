<?php

use App\Http\Controllers\TagController;
use App\Http\Controllers\RecipeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index'])->name('tags.index');
    Route::get('/{tag}', [TagController::class, 'show'])->name('tags.show');
    Route::delete('/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
});

Route::prefix('recipes')->group(function () {
    Route::get('/', [RecipeController::class, 'index'])->name('recipes.index');
    Route::post('/', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
    Route::put('/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
});
