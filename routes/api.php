<?php

use Celios\Core\Http\Controllers\Api\V1\AuthController;
use Celios\Core\Http\Controllers\Api\V1\CategoryController;
use Celios\Core\Http\Controllers\Api\V1\CrmContactController;
use Celios\Core\Http\Controllers\Api\V1\DocumentCategoryController;
use Celios\Core\Http\Controllers\Api\V1\DocumentController;
use Celios\Core\Http\Controllers\Api\V1\FormController;
use Celios\Core\Http\Controllers\Api\V1\MediaController;
use Celios\Core\Http\Controllers\Api\V1\MenuController;
use Celios\Core\Http\Controllers\Api\V1\NewsletterController;
use Celios\Core\Http\Controllers\Api\V1\PageController;
use Celios\Core\Http\Controllers\Api\V1\PaymentController;
use Celios\Core\Http\Controllers\Api\V1\PostController;
use Celios\Core\Http\Controllers\Api\V1\ProfileController;
use Celios\Core\Http\Controllers\Api\V1\SectionController;
use Celios\Core\Http\Controllers\Api\V1\SettingsController;
use Celios\Core\Http\Controllers\Api\V1\SystemController;
use Celios\Core\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - V1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ==========================================
    // AUTHENTICATION & PROFILE
    // ==========================================
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:api-login');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware('auth:sanctum')->prefix('profile')->group(function () {
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
        Route::delete('/', [ProfileController::class, 'destroy']);
    });

    // ==========================================
    // PUBLIC SETTINGS, PAGES & MENUS
    // ==========================================
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/pages/home', [PageController::class, 'home']);
    Route::get('/pages/{slug}', [PageController::class, 'show']);

    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/{key}', [MenuController::class, 'show']);

    // ==========================================
    // BLOG & CATEGORIES (PUBLIC)
    // ==========================================
    Route::middleware('module:blog')->group(function () {
        Route::get('/posts', [PostController::class, 'index']);
        Route::get('/posts/{slugOrId}', [PostController::class, 'show']);

        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{slugOrId}', [CategoryController::class, 'show']);
    });

    // ==========================================
    // DOCUMENTS (PUBLIC)
    // ==========================================
    Route::middleware('module:documents')->group(function () {
        Route::get('/document-categories', [DocumentCategoryController::class, 'index']);
        Route::get('/document-categories/{slugOrId}', [DocumentCategoryController::class, 'show']);

        Route::get('/documents', [DocumentController::class, 'index']);
        Route::get('/documents/{slugOrId}', [DocumentController::class, 'show']);
        Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
        Route::get('/documents/{document}/preview', [DocumentController::class, 'preview']);
    });

    // ==========================================
    // FORMS (PUBLIC)
    // ==========================================
    Route::middleware('module:forms')->group(function () {
        Route::get('/forms/{slug}', [FormController::class, 'show']);
        Route::post('/forms/{slug}/submit', [FormController::class, 'submit'])
            ->middleware('throttle:form-submissions');
    });

    // ==========================================
    // NEWSLETTER (PUBLIC)
    // ==========================================
    Route::middleware('module:newsletter')->group(function () {
        Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
            ->middleware('throttle:newsletter-subscribe');
        Route::get('/newsletter/verify/{token}', [NewsletterController::class, 'verify']);
        Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe']);
    });

    // ==========================================
    // PAYMENTS (PUBLIC / CHECKOUT)
    // ==========================================
    Route::middleware('module:payment')->group(function () {
        Route::get('/payment-gateways', [PaymentController::class, 'gateways']);
        Route::post('/payments/checkout', [PaymentController::class, 'checkout']);
    });

    // ==========================================
    // ADMIN / BACK-OFFICE ENDPOINTS
    // ==========================================
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

        // Settings
        Route::put('/settings', [SettingsController::class, 'update']);

        // Pages & Revisions
        Route::get('/pages', [PageController::class, 'index']);
        Route::post('/pages', [PageController::class, 'store']);
        Route::get('/pages/{page}', [PageController::class, 'show']);
        Route::put('/pages/{page}', [PageController::class, 'update']);
        Route::delete('/pages/{page}', [PageController::class, 'destroy']);
        Route::post('/pages/{page}/draft', [PageController::class, 'saveDraft']);
        Route::post('/pages/{page}/publish', [PageController::class, 'publish']);
        Route::get('/pages/{page}/revisions', [PageController::class, 'revisions']);
        Route::post('/pages/{page}/revisions/{revision}/restore', [PageController::class, 'restoreRevision']);

        // Global Reusable Sections
        Route::get('/sections', [SectionController::class, 'index']);
        Route::post('/sections', [SectionController::class, 'store']);
        Route::get('/sections/{section}', [SectionController::class, 'show']);
        Route::put('/sections/{section}', [SectionController::class, 'update']);
        Route::delete('/sections/{section}', [SectionController::class, 'destroy']);

        // Menus & Menu Items
        Route::get('/menus', [MenuController::class, 'index']);
        Route::post('/menus', [MenuController::class, 'store']);
        Route::get('/menus/{menu}', [MenuController::class, 'show']);
        Route::put('/menus/{menu}', [MenuController::class, 'update']);
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy']);
        Route::post('/menus/{menu}/items', [MenuController::class, 'storeItem']);
        Route::put('/menus/{menu}/items/{item}', [MenuController::class, 'updateItem']);
        Route::delete('/menus/{menu}/items/{item}', [MenuController::class, 'destroyItem']);

        // Media Library
        Route::get('/media', [MediaController::class, 'index']);
        Route::post('/media', [MediaController::class, 'store']);
        Route::get('/media/{medium}', [MediaController::class, 'show']);
        Route::delete('/media/{medium}', [MediaController::class, 'destroy']);

        // Users
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        // System (Activity Logs & Notifications)
        Route::get('/activities', [SystemController::class, 'activities']);
        Route::get('/notifications', [SystemController::class, 'notifications']);
        Route::post('/notifications/{id}/read', [SystemController::class, 'markNotificationRead']);

        // Blog & Categories
        Route::middleware('module:blog')->group(function () {
            Route::get('/posts', [PostController::class, 'index']);
            Route::post('/posts', [PostController::class, 'store']);
            Route::get('/posts/{post}', [PostController::class, 'show']);
            Route::put('/posts/{post}', [PostController::class, 'update']);
            Route::delete('/posts/{post}', [PostController::class, 'destroy']);

            Route::get('/categories', [CategoryController::class, 'index']);
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::get('/categories/{category}', [CategoryController::class, 'show']);
            Route::put('/categories/{category}', [CategoryController::class, 'update']);
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
        });

        // Documents & Document Categories
        Route::middleware('module:documents')->group(function () {
            Route::get('/document-categories', [DocumentCategoryController::class, 'index']);
            Route::post('/document-categories', [DocumentCategoryController::class, 'store']);
            Route::get('/document-categories/{category}', [DocumentCategoryController::class, 'show']);
            Route::put('/document-categories/{category}', [DocumentCategoryController::class, 'update']);
            Route::delete('/document-categories/{category}', [DocumentCategoryController::class, 'destroy']);

            Route::get('/documents', [DocumentController::class, 'index']);
            Route::post('/documents', [DocumentController::class, 'store']);
            Route::get('/documents/{document}', [DocumentController::class, 'show']);
            Route::put('/documents/{document}', [DocumentController::class, 'update']);
            Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
        });

        // Forms & Submissions
        Route::middleware('module:forms')->group(function () {
            Route::get('/forms', [FormController::class, 'index']);
            Route::post('/forms', [FormController::class, 'store']);
            Route::get('/forms/{form}', [FormController::class, 'show']);
            Route::put('/forms/{form}', [FormController::class, 'update']);
            Route::delete('/forms/{form}', [FormController::class, 'destroy']);

            Route::get('/forms/{form}/submissions', [FormController::class, 'submissions']);
            Route::post('/forms/{form}/submissions/{submission}/read', [FormController::class, 'markSubmissionRead']);
            Route::delete('/forms/{form}/submissions/{submission}', [FormController::class, 'destroySubmission']);
        });

        // Newsletter Subscribers & Campaigns
        Route::middleware('module:newsletter')->group(function () {
            Route::get('/newsletter/subscribers', [NewsletterController::class, 'subscribers']);
            Route::delete('/newsletter/subscribers/{subscriber}', [NewsletterController::class, 'destroySubscriber']);

            Route::get('/newsletter/campaigns', [NewsletterController::class, 'campaigns']);
            Route::post('/newsletter/campaigns', [NewsletterController::class, 'storeCampaign']);
            Route::get('/newsletter/campaigns/{campaign}', [NewsletterController::class, 'showCampaign']);
            Route::put('/newsletter/campaigns/{campaign}', [NewsletterController::class, 'updateCampaign']);
            Route::delete('/newsletter/campaigns/{campaign}', [NewsletterController::class, 'destroyCampaign']);
        });

        // CRM Contacts & Notes
        Route::middleware('module:crm')->group(function () {
            Route::get('/crm/contacts', [CrmContactController::class, 'index']);
            Route::post('/crm/contacts', [CrmContactController::class, 'store']);
            Route::get('/crm/contacts/{contact}', [CrmContactController::class, 'show']);
            Route::put('/crm/contacts/{contact}', [CrmContactController::class, 'update']);
            Route::delete('/crm/contacts/{contact}', [CrmContactController::class, 'destroy']);
            Route::post('/crm/contacts/{contact}/notes', [CrmContactController::class, 'storeNote']);
        });

        // Payments
        Route::middleware('module:payment')->group(function () {
            Route::get('/payments', [PaymentController::class, 'index']);
            Route::get('/payments/{payment}', [PaymentController::class, 'show']);
            Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund']);
        });
    });

});
