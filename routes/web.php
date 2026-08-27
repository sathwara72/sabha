<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BusinessController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\EventController;
use App\Http\Controllers\Web\GalleryController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/trustees', [PageController::class, 'trustees'])->name('trustees');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/businesses', [BusinessController::class, 'index'])->name('businesses.index');
Route::get('/businesses/{business}', [BusinessController::class, 'show'])->name('businesses.show');
Route::post('/businesses/{business}/reviews', [BusinessController::class, 'storeReview'])
    ->middleware(['auth', 'registration.complete'])
    ->name('businesses.reviews.store');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event}/reserve', [EventController::class, 'reserve'])
    ->middleware(['auth', 'registration.complete'])
    ->name('events.reserve');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/event/{id}', [GalleryController::class, 'event'])->name('gallery.event');

Route::get('/login', [AuthController::class, 'loginRedirect'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/register', [AuthController::class, 'registerPage'])->name('register');
Route::get('/forgot-password', [AuthController::class, 'forgotPasswordPage'])->name('forgot-password');
Route::post('/forgot-password/send-otp', [AuthController::class, 'forgotPasswordSendOtp'])->middleware('throttle:3,1')->name('forgot-password.send-otp');
Route::post('/forgot-password/reset', [AuthController::class, 'forgotPasswordReset'])->middleware('throttle:6,1')->name('forgot-password.reset');

Route::middleware('registration.complete')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/events/{id}', [ProfileController::class, 'eventShow'])->name('profile.events.show');
    Route::get('/profile/events/{id}/ticket.png', [ProfileController::class, 'downloadTicket'])->name('profile.events.ticket');
    Route::view('/profile/meetings/create', 'pages.profile-meeting-form')->name('profile.meetings.create');
    Route::view('/profile/meetings/{id}/edit', 'pages.profile-meeting-form')->name('profile.meetings.edit');

    Route::view('/chat', 'pages.chat')->name('chat.index');
    Route::view('/chat/{id}', 'pages.chat')->name('chat.show');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::view('/', 'pages.admin.dashboard')->name('dashboard');

    Route::middleware('module:businesses')->group(function () {
        Route::view('/businesses', 'pages.admin.businesses')->name('businesses.index');
        Route::view('/businesses/{id}', 'pages.admin.business-show')->name('businesses.show');
    });
    Route::middleware('module:events')->group(function () {
        Route::view('/events', 'pages.admin.events')->name('events.index');
        Route::view('/events/create', 'pages.admin.event-form')->name('events.create');
        Route::view('/events/{id}/edit', 'pages.admin.event-form')->name('events.edit');
        Route::view('/events/{id}', 'pages.admin.event-show')->name('events.show');
    });
    Route::view('/bookings', 'pages.admin.bookings')->name('bookings.index')->middleware('module:bookings');
    Route::view('/users', 'pages.admin.users')->name('users.index')->middleware('module:users');
    Route::view('/registrations', 'pages.admin.registrations')->name('registrations.index')->middleware('module:registrations');
    Route::middleware('module:gallery')->group(function () {
        Route::view('/gallery', 'pages.admin.gallery')->name('gallery.index');
        Route::view('/gallery/create', 'pages.admin.gallery-form')->name('gallery.create');
    });
    Route::middleware('module:hero-slider')->group(function () {
        Route::view('/hero-slider', 'pages.admin.hero-slider')->name('hero-slider.index');
        Route::view('/hero-slider/create', 'pages.admin.hero-slider-form')->name('hero-slider.create');
        Route::view('/hero-slider/{id}/edit', 'pages.admin.hero-slider-form')->name('hero-slider.edit');
    });
    Route::middleware('module:categories')->group(function () {
        Route::view('/categories', 'pages.admin.categories')->name('categories.index');
        Route::view('/categories/create', 'pages.admin.category-form')->name('categories.create');
        Route::view('/categories/{id}/edit', 'pages.admin.category-form')->name('categories.edit');
    });
    Route::middleware('module:locations')->group(function () {
        Route::view('/locations', 'pages.admin.locations')->name('locations.index');
        Route::view('/locations/create', 'pages.admin.location-form')->name('locations.create');
        Route::view('/locations/{id}/edit', 'pages.admin.location-form')->name('locations.edit');
        Route::view('/locations/{id}/areas', 'pages.admin.location-areas')->name('locations.areas');
    });
    Route::middleware('module:member-titles')->group(function () {
        Route::view('/member-titles', 'pages.admin.member-titles')->name('member-titles.index');
        Route::view('/member-titles/create', 'pages.admin.member-title-form')->name('member-titles.create');
        Route::view('/member-titles/{id}/edit', 'pages.admin.member-title-form')->name('member-titles.edit');
    });
    Route::middleware('module:trustees')->group(function () {
        Route::view('/trustees', 'pages.admin.trustees')->name('trustees.index');
        Route::view('/trustees/create', 'pages.admin.trustee-form')->name('trustees.create');
        Route::view('/trustees/{id}/edit', 'pages.admin.trustee-form')->name('trustees.edit');
    });
    Route::view('/meetings', 'pages.admin.meetings')->name('meetings.index')->middleware('module:meetings');
    Route::view('/referrals', 'pages.admin.referrals')->name('referrals.index')->middleware('module:referrals');
    Route::view('/testimonials', 'pages.admin.testimonials')->name('testimonials.index')->middleware('module:testimonials');
    Route::view('/statistics', 'pages.admin.statistics')->name('statistics.index')->middleware('module:statistics');
    Route::view('/analytics', 'pages.admin.analytics')->name('analytics.index')->middleware('module:analytics');

    Route::middleware('full-admin')->group(function () {
        Route::view('/settings', 'pages.admin.settings')->name('settings.index');
        Route::view('/sub-admins', 'pages.admin.sub-admins')->name('sub-admins.index');
        Route::view('/sub-admins/create', 'pages.admin.sub-admin-form')->name('sub-admins.create');
        Route::view('/sub-admins/{id}/edit', 'pages.admin.sub-admin-form')->name('sub-admins.edit');
    });
});

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'gu'], true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('lang.switch');
