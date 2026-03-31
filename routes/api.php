<?php

use App\Http\Controllers\Api\ArticleController;
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

// Public article list – no API key required; service-level visibility enforced
Route::get('/articles', [ArticleController::class, 'index']);

// Article by path – optional API key; service-level visibility enforced
Route::get('/articles/by-path/{path}', [ArticleController::class, 'showByPath'])
    ->where('path', '.*');

// Article by ID – no forced authentication; private articles accessible with API key
Route::get('/articles/{id}', [ArticleController::class, 'show']);

// Protected routes – API key required
Route::middleware('api.key')->group(function () {
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::put('/articles/{id}', [ArticleController::class, 'update']);
});
