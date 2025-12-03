<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AdminBookingController; 
use App\Http\Controllers\AdminProfileController; 
use App\Http\Controllers\AdminDashboardController; 
use App\Http\Controllers\BuildingController; 
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {return redirect()->route('login');});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// 1. PUBLIC ROUTES (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// 2. SHARED ROUTES (Anyone logged in)
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
});

// 3. STUDENT & LECTURER ONLY
Route::middleware(['auth', 'role:student|lecturer'])->group(function () {
    Route::view('/users/dashboard', 'users.dashboard')->name('users.dashboard');

    Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/{id}', [FacilityController::class, 'show'])->name('facilities.show');
    
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/bookings/history-search', [SearchController::class, 'index'])->name('history.search');
    Route::post('/bookings/search-xml', [BookingController::class, 'searchHistory'])->name('bookings.search_xml');

    Route::get('/history', [SearchController::class, 'index'])->name('history');
    Route::get('/bookings/search', [SearchController::class, 'search']);
    Route::get('/booking/{id}/ticket', [BookingController::class, 'downloadTicket'])->name('booking.ticket');
    Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');

    Route::get('/bookings/history', [SearchController::class, 'index'])->name('history');

    // ENHANCEMENT: Apply Rate Limiting here
    Route::get('/bookings/search', [SearchController::class, 'search'])
        ->name('bookings.search')
        ->middleware('throttle:100,1'); // 100 requests per minute
});

// 4. ADMIN ONLY
Route::middleware(['auth', 'role:admin'])->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'show'])->name('dashboard');
    Route::get('/admin/assets/report', [AssetController::class, 'generateReport'])->name('admin.assets.report');
    Route::get('/admin/profile', [AdminProfileController::class, 'show'])->name('admin.profile');
    Route::get('/admin/profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::get('/bookings/bookingapproval', [AdminBookingController::class, 'bookings'])->name('bookings.approval');

    // Admin Actions (Prefix /admin and name admin.xxx)
    Route::prefix('admin')->name('admin.')->group(function () {
        
        Route::post('/bookings/{id}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{id}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');
        Route::get('/bookings/{id}', [AdminBookingController::class, 'show'])->name('bookings.show');

        Route::get('/facilities/manage', [FacilityController::class, 'manage'])->name('facilities.manage');
        Route::get('/facilities/{id}/edit', [FacilityController::class, 'edit'])->name('facilities.edit');
        Route::put('/facilities/{id}', [FacilityController::class, 'update'])->name('facilities.update');
        Route::delete('/facilities/{id}', [FacilityController::class, 'destroy'])->name('facilities.destroy');
        Route::get('/facilities/create', [FacilityController::class, 'create'])->name('facilities.create');
        Route::post('/facilities/store', [FacilityController::class, 'store'])->name('facilities.store');
        Route::post('/facilities', [FacilityController::class, 'store'])->name('facilities.store');

        Route::get('/buildings/manage', [BuildingController::class, 'manage'])->name('buildings.manage');
        Route::get('/buildings/create', [BuildingController::class, 'create'])->name('buildings.create');
        Route::get('/buildings/{id}/edit', [BuildingController::class, 'edit'])->name('buildings.edit');
        Route::put('/buildings/{id}', [BuildingController::class, 'update'])->name('buildings.update');
        Route::delete('/buildings/{id}', [BuildingController::class, 'destroy'])->name('buildings.destroy');
        Route::post('/buildings/store', [BuildingController::class, 'store'])->name('buildings.store');
        Route::post('/buildings', [BuildingController::class, 'store'])->name('buildings.store');

        Route::get('/assets/manage', [AssetController::class, 'manage'])->name('assets.manage');
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::get('/assets/{id}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('assets.destroy');
        Route::post('/assets/store', [AssetController::class, 'store'])->name('assets.store');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/admin/assets/{id}', [AssetController::class, 'show'])->name('assets.show');

        Route::get('/users/index/', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}/edit/', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::put('/users/credits/{id}', [UserController::class, 'credits'])->name('users.credits');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
});