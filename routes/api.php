<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\SearchController;

// ============================================================
// PUBLIC API ROUTES (No Authentication Required)
// ============================================================

// REST API - JSON Login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ============================================================
// PROTECTED API ROUTES (Require Authentication)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Module 2: Get User Credits (AJAX)
    Route::get('/user/credits', [UserProfileController::class, 'getCredits']);

    // Module 3: Live Search API
    Route::get('/facilities/search', [FacilityController::class, 'search']);
    Route::get('/user', function (Request $request) {
        return $request->user();

    Route::get('/search', [SearchController::class, 'search'])
        ->middleware('throttle:100,1'); 
    });
});