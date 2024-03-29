<?php

use App\Http\Controllers\TagController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthentificationController;

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
    Route::prefix('/recipes')->group(function () {
        Route::get('/', [RecipeController::class, 'index'])->name('recipes.index');
        Route::post('/', [RecipeController::class, 'store'])->name('recipes.store');
        Route::get('/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
        Route::put('/edit/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
        Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
        Route::post('/{recipe}/steps', [RecipeController::class, 'addStep']);
        Route::get('/{recipe}/steps', [RecipeController::class, 'listSteps']);
        Route::PATCH('/{recipe}/steps/{step}', [RecipeController::class, 'updateStep']);
        Route::delete('/{recipe}/steps/{step}', [RecipeController::class, 'deleteStep']);

        Route::post('/recipes/{recipe}/ingredients', [IngredientController::class, 'addToRecipe']);

    });
    Route::get('/recipes/user/{id}', [RecipeController::class, 'getRecipesByUser']);
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/logout', [AuthentificationController::class, 'logout'])->name('logout');
});

Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index'])->name('tags.index');
    Route::get('/{tag}', [TagController::class, 'show'])->name('tags.show');
    Route::delete('/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
    Route::get('/type', [TagController::class, 'showTagType'])->name('tags.Types');
    Route::get('/type/{type}', [TagController::class, 'getTagsByType'])->name('tags.byType');
});


Route::get('/recipes/ingredients/search', [IngredientController::class, 'search']);
Route::get('/ingredients', [IngredientController::class, 'index']);

Route::get('/recipes/{recipe}/steps', [RecipeController::class, 'getStepsByRecipe']);
Route::get('/recipes/{recipe}/ingredients', [RecipeController::class, 'getIngredientsByRecipe']);
Route::get('/recipes/{recipe}/tags', [RecipeController::class, 'getTagsByRecipe']);

Route::post('/login', [AuthentificationController::class, 'login'])->name('login');
Route::post('/register', [AuthentificationController::class, 'register'])->name('register');


