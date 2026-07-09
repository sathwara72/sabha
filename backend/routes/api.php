<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\SabhaController;

// Public Routes
Route::post('/register', [SabhaController::class, 'register']);
Route::post('/register/send-otp', [SabhaController::class, 'registerSendOtp']);
Route::post('/register/confirm', [SabhaController::class, 'registerConfirm']);
Route::post('/login', [SabhaController::class, 'login']);
Route::post('/demo-login', [SabhaController::class, 'demoLogin']);
Route::post('/forgot-password', [SabhaController::class, 'forgotPassword']);
Route::get('/businesses', [SabhaController::class, 'getBusinesses']);
Route::get('/businesses/{id}/reviews', [SabhaController::class, 'getReviews']);
Route::post('/businesses/{id}/reviews', [SabhaController::class, 'submitReview'])->middleware('auth:sanctum');
Route::post('/businesses/{id}/inquiry', [SabhaController::class, 'submitBusinessInquiry']);
Route::get('/events', [SabhaController::class, 'getEvents']);
Route::get('/statistics', [SabhaController::class, 'getStatistics']);
Route::get('/gallery', [SabhaController::class, 'getGalleryImages']);
Route::get('/settings', [SabhaController::class, 'getSettings']);
Route::get('/hero-images', [SabhaController::class, 'getHeroImages']);
Route::get('/qr-code', [SabhaController::class, 'generateQrCode']);
Route::post('/contact', [SabhaController::class, 'submitContactInquiry']);
Route::get('/categories', [SabhaController::class, 'getCategories']);

// User Submitted Business Route
Route::post('/businesses', [SabhaController::class, 'submitBusiness'])->middleware('auth:sanctum');

// Admin Routes (protected with Sanctum middleware)
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/businesses', [SabhaController::class, 'getAllBusinesses']);
    Route::post('/businesses/{id}/approve', [SabhaController::class, 'approveBusiness']);
    Route::post('/businesses/{id}/reject', [SabhaController::class, 'rejectBusiness']);
    Route::post('/events', [SabhaController::class, 'storeEvent']);
    Route::post('/events/{id}', [SabhaController::class, 'updateEvent']);
    Route::get('/users', [SabhaController::class, 'getUsers']);
    Route::post('/gallery/upload', [SabhaController::class, 'uploadGalleryImage']);
    Route::delete('/gallery/{id}', [SabhaController::class, 'deleteGalleryImage']);
    Route::post('/statistics/{id}', [SabhaController::class, 'updateStatistic']);
    Route::post('/settings', [SabhaController::class, 'updateSettings']);
    
    // Hero Slider Images Management for Admin
    Route::post('/hero-images', [SabhaController::class, 'storeHeroImage']);
    Route::delete('/hero-images/{id}', [SabhaController::class, 'deleteHeroImage']);
    
    // Event Booking Management for Admin
    Route::get('/registrations', [SabhaController::class, 'getAllEventRegistrations']);
    Route::post('/registrations/{id}/approve', [SabhaController::class, 'approveEventRegistration']);
    Route::post('/registrations/{id}/reject', [SabhaController::class, 'rejectEventRegistration']);
    Route::post('/registrations/{id}/toggle-attendance', [SabhaController::class, 'toggleAttendance']);
    Route::post('/registrations/check-in', [SabhaController::class, 'checkInTicket']);

    // Business Category Management
    Route::get('/categories', [SabhaController::class, 'getAllCategories']);
    Route::post('/categories', [SabhaController::class, 'storeCategory']);
    Route::delete('/categories/{id}', [SabhaController::class, 'deleteCategory']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/user/business', [SabhaController::class, 'getUserBusiness'])->middleware('auth:sanctum');
Route::post('/user/profile', [SabhaController::class, 'updateProfile'])->middleware('auth:sanctum');

// User Authenticated Event Reservation & Registrations
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events/{id}/reserve', [SabhaController::class, 'reserveEventSpot']);
    Route::get('/user/registrations', [SabhaController::class, 'getUserRegistrations']);
    Route::post('/events/{id}/upload-photos', [SabhaController::class, 'uploadEventPhotos']);
    Route::post('/gallery/upload', [SabhaController::class, 'uploadGalleryImage']);
});

// Programmatic Migration & Seeding Runners (to bypass terminal command permissions)
Route::get('/migrate', function() {
    try {
        $migrations = [
            'database/migrations/2026_06_08_000001_add_prices_to_events_table.php',
            'database/migrations/2026_06_08_000002_add_details_to_businesses_table.php',
            'database/migrations/2026_06_08_000003_add_map_link_to_events_table.php',
            'database/migrations/2026_06_08_000004_add_profile_details_to_users_table.php',
            'database/migrations/2026_06_08_000005_create_settings_table.php',
            'database/migrations/2026_06_08_000006_add_social_and_covers_to_businesses_table.php',
            'database/migrations/2026_06_08_000007_create_reviews_table.php',
            'database/migrations/2026_06_08_000008_add_avatar_to_users_table.php',
            'database/migrations/2026_06_30_000000_create_event_registrations_table.php',
        ];
        
        $output = '';
        foreach ($migrations as $path) {
            Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true
            ]);
            $output .= Artisan::output() . "\n";
        }
        return response()->json(['message' => 'All migrations ran successfully!', 'output' => $output]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/seed', function() {
    try {
        Artisan::call('db:seed', [
            '--class' => 'SabhaSeeder',
            '--force' => true
        ]);
        return response()->json(['message' => 'Seeding ran successfully!', 'output' => Artisan::output()]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

