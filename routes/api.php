<?php

use Celios\Core\Http\Controllers\Api\V1\AuthController;
use Celios\Core\Http\Controllers\Api\V1\CategoryController;
use Celios\Core\Http\Controllers\Api\V1\DocumentController;
use Celios\Core\Http\Controllers\Api\V1\FormController;
use Celios\Core\Http\Controllers\Api\V1\MenuController;
use Celios\Core\Http\Controllers\Api\V1\NewsletterController;
use Celios\Core\Http\Controllers\Api\V1\PageController;
use Celios\Core\Http\Controllers\Api\V1\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - V1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ==========================================
    // AUTHENTICATION
    // ==========================================
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // ==========================================
    // BLOG & CATEGORIES
    // ==========================================
    Route::middleware('module:blog')->group(function () {
        Route::get('/posts', [PostController::class, 'index']);
        Route::get('/posts/{slugOrId}', [PostController::class, 'show']);

        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{slugOrId}', [CategoryController::class, 'show']);
    });

    // ==========================================
    // CMS PAGES & MENUS
    // ==========================================
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    Route::get('/menus/{key}', [MenuController::class, 'show']);

    // ==========================================
    // DOCUMENTS
    // ==========================================
    Route::middleware('module:documents')->group(function () {
        Route::get('/documents', [DocumentController::class, 'index']);
        Route::get('/documents/{slugOrId}', [DocumentController::class, 'show']);
        Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
    });

    // ==========================================
    // FORMS
    // ==========================================
    Route::middleware('module:forms')->group(function () {
        Route::post('/forms/{slug}/submit', [FormController::class, 'submit']);
    });

    // ==========================================
    // NEWSLETTER
    // ==========================================
    Route::middleware('module:newsletter')->group(function () {
        Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
    });

});
