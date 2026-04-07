<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\SabhaController;

Route::get('/businesses', [SabhaController::class, 'getBusinesses']);
Route::get('/events', [SabhaController::class, 'getEvents']);
Route::get('/statistics', [SabhaController::class, 'getStatistics']);

// Admin Routes (To be protected with middleware in production)
Route::prefix('admin')->group(function () {
    Route::get('/businesses', [SabhaController::class, 'getAllBusinesses']);
    Route::post('/businesses/{id}/approve', [SabhaController::class, 'approveBusiness']);
    Route::post('/businesses/{id}/reject', [SabhaController::class, 'rejectBusiness']);
    Route::post('/events', [SabhaController::class, 'storeEvent']);
    Route::get('/users', [SabhaController::class, 'getUsers']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
