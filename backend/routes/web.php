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
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/businesses', [BusinessController::class, 'index'])->name('businesses.index');
Route::get('/businesses/{business}', [BusinessController::class, 'show'])->name('businesses.show');
Route::post('/businesses/{business}/reviews', [BusinessController::class, 'storeReview'])
    ->middleware('auth')
    ->name('businesses.reviews.store');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event}/reserve', [EventController::class, 'reserve'])
    ->middleware('auth')
    ->name('events.reserve');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/event/{id}', [GalleryController::class, 'event'])->name('gallery.event');

Route::get('/login', [AuthController::class, 'loginRedirect'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/register', [AuthController::class, 'registerPage'])->name('register');
Route::post('/register/send-otp', [AuthController::class, 'registerSendOtp'])->middleware('throttle:3,1')->name('register.send-otp');
Route::post('/register/confirm', [AuthController::class, 'registerConfirm'])->middleware('throttle:6,1')->name('register.confirm');
Route::get('/forgot-password', [AuthController::class, 'forgotPasswordPage'])->name('forgot-password');
Route::post('/forgot-password/send-otp', [AuthController::class, 'forgotPasswordSendOtp'])->middleware('throttle:3,1')->name('forgot-password.send-otp');
Route::post('/forgot-password/reset', [AuthController::class, 'forgotPasswordReset'])->middleware('throttle:6,1')->name('forgot-password.reset');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/profile/events/{id}', [ProfileController::class, 'eventShow'])->name('profile.events.show');
Route::get('/profile/events/{id}/ticket.png', [ProfileController::class, 'downloadTicket'])->name('profile.events.ticket');

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::view('/', 'pages.admin.dashboard')->name('dashboard');

    Route::middleware('module:businesses')->group(function () {
        Route::view('/businesses', 'pages.admin.businesses')->name('businesses.index');
        Route::view('/businesses/{id}', 'pages.admin.business-show')->name('businesses.show');
    });
    Route::middleware('module:events')->group(function () {
        Route::view('/events', 'pages.admin.events')->name('events.index');
        Route::view('/events/{id}', 'pages.admin.event-show')->name('events.show');
    });
    Route::view('/bookings', 'pages.admin.bookings')->name('bookings.index')->middleware('module:bookings');
    Route::view('/users', 'pages.admin.users')->name('users.index')->middleware('module:users');
    Route::view('/gallery', 'pages.admin.gallery')->name('gallery.index')->middleware('module:gallery');
    Route::view('/hero-slider', 'pages.admin.hero-slider')->name('hero-slider.index')->middleware('module:hero-slider');
    Route::view('/categories', 'pages.admin.categories')->name('categories.index')->middleware('module:categories');
    Route::view('/locations', 'pages.admin.locations')->name('locations.index')->middleware('module:locations');
    Route::view('/member-titles', 'pages.admin.member-titles')->name('member-titles.index')->middleware('module:member-titles');
    Route::view('/meetings', 'pages.admin.meetings')->name('meetings.index')->middleware('module:meetings');
    Route::view('/referrals', 'pages.admin.referrals')->name('referrals.index')->middleware('module:referrals');
    Route::view('/testimonials', 'pages.admin.testimonials')->name('testimonials.index')->middleware('module:testimonials');
    Route::view('/statistics', 'pages.admin.statistics')->name('statistics.index')->middleware('module:statistics');
    Route::view('/analytics', 'pages.admin.analytics')->name('analytics.index')->middleware('module:analytics');

    Route::middleware('full-admin')->group(function () {
        Route::view('/settings', 'pages.admin.settings')->name('settings.index');
        Route::view('/sub-admins', 'pages.admin.sub-admins')->name('sub-admins.index');
    });
});

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'gu'], true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('lang.switch');
