<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'log.action'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Listing Routes
    Route::get('listings', [ListingController::class, 'index']);
    Route::post('listings', [ListingController::class, 'store']);
    Route::get('listings/{listing}', [ListingController::class, 'show']);
    Route::put('listings/{listing}', [ListingController::class, 'update']);
    Route::delete('listings/{listing}', [ListingController::class, 'destroy']);

    // Image Routes
    Route::post('listings/{listing}/images', [ImageController::class, 'storeListingImages']);
    Route::post('users/{user}/profile-image', [ImageController::class, 'storeUserProfileImage']);
});

// Public Route
Route::get('featured-listings', [ListingController::class, 'featuredListings']);
