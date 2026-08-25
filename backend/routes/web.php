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
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/register', [AuthController::class, 'registerPage'])->name('register');
Route::post('/register/send-otp', [AuthController::class, 'registerSendOtp'])->name('register.send-otp');
Route::post('/register/confirm', [AuthController::class, 'registerConfirm'])->name('register.confirm');
Route::get('/forgot-password', [AuthController::class, 'forgotPasswordPage'])->name('forgot-password');
Route::post('/forgot-password/send-otp', [AuthController::class, 'forgotPasswordSendOtp'])->name('forgot-password.send-otp');
Route::post('/forgot-password/reset', [AuthController::class, 'forgotPasswordReset'])->name('forgot-password.reset');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/profile/events/{id}', [ProfileController::class, 'eventShow'])->name('profile.events.show');

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::view('/', 'pages.admin.dashboard')->name('dashboard');
    Route::view('/businesses', 'pages.admin.businesses')->name('businesses.index');
    Route::view('/businesses/{id}', 'pages.admin.business-show')->name('businesses.show');
    Route::view('/events', 'pages.admin.events')->name('events.index');
    Route::view('/events/{id}', 'pages.admin.event-show')->name('events.show');
    Route::view('/bookings', 'pages.admin.bookings')->name('bookings.index');
    Route::view('/users', 'pages.admin.users')->name('users.index');
    Route::view('/gallery', 'pages.admin.gallery')->name('gallery.index');
    Route::view('/hero-slider', 'pages.admin.hero-slider')->name('hero-slider.index');
    Route::view('/categories', 'pages.admin.categories')->name('categories.index');
    Route::view('/statistics', 'pages.admin.statistics')->name('statistics.index');
    Route::view('/settings', 'pages.admin.settings')->name('settings.index');
});

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'gu'], true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('lang.switch');
