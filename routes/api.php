<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Api\HolidayController;

// ============================================================
// PUBLIC API ROUTES (No Authentication Required)
// ============================================================

// REST API - JSON Login with IFA Compliance (Registration handled by Admin only)
Route::post('/login', [AuthController::class, 'login'])->middleware('ifa');

// Public Holiday API (No auth required - for frontend date picker)
Route::get('/holidays/{year}', [HolidayController::class, 'index']);
Route::get('/holidays/check/{date}', [HolidayController::class, 'check']);

// ============================================================
// PROTECTED API ROUTES (Require Authentication)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {
    // Logout - IFA COMPLIANT
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('ifa');
    
    // Module 2: Get User Credits (AJAX) - IFA COMPLIANT
    Route::get('/user/credits', [UserProfileController::class, 'getCredits'])
        ->middleware('ifa');

    // Module 3: Live Search API - IFA COMPLIANT
    Route::get('/facilities/search', [FacilityController::class, 'search'])
        ->middleware('ifa');
    
    // Get authenticated user - IFA COMPLIANT
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('ifa');

    // Booking search - IFA COMPLIANT
    Route::get('/search', [SearchController::class, 'search'])
        ->middleware(['ifa', 'throttle:100,1']);
});