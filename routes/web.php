<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\EnquiryController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/our-work', 'pages.work')->name('work');
Route::view('/stories', 'pages.stories')->name('stories');
Route::get('/stories/{slug}', function (string $slug) {
    $story = config('idealwoman.stories.'.$slug);
    abort_unless(is_array($story), 404);

    return view('pages.story', compact('story', 'slug'));
})->where('slug', '[a-z0-9-]+')->name('stories.show');
Route::view('/get-involved', 'pages.involved')->name('involved');
Route::view('/volunteer-with-us', 'pages.volunteer')->name('volunteer');
Route::view('/contact', 'pages.contact')->name('contact');
Route::post('/contact', [EnquiryController::class, 'store'])->middleware('throttle:5,10')->name('enquiries.store');
Route::get('/sitemap.xml', function () {
    return response()->view('sitemap')->header('Content-Type', 'application/xml');
})->name('sitemap');
Route::get('/robots.txt', function () {
    $content = config('idealwoman.indexable')
        ? "User-agent: *\nAllow: /\nSitemap: ".route('sitemap')."\n"
        : "User-agent: *\nDisallow: /\n";

    return response($content)->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/media/{path}', [MediaController::class, 'show'])->name('media.show');
Route::prefix('admin')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:admin-login')->name('admin.login');
    Route::middleware('guest')->group(function (): void {
        Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
        Route::post('/forgot-password', [PasswordResetController::class, 'send'])->middleware('throttle:admin-password-reset')->name('password.email');
        Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
        Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:admin-password-reset')->name('password.update');
    });
    Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('admin.logout');
    Route::middleware(['auth', 'can:manage-content'])->name('admin.')->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/content/{section}', [ContentController::class, 'edit'])->name('content.edit');
        Route::put('/content/{section}', [ContentController::class, 'update'])->name('content.update');
        Route::post('/content/{section}/restore/{revision}', [ContentController::class, 'restore'])->name('content.restore');
        Route::resource('stories', StoryController::class)->except(['show', 'destroy']);
        Route::post('/stories/{story}/unpublish', [StoryController::class, 'unpublish'])->name('stories.unpublish');
        Route::get('/media', [MediaController::class, 'index'])->name('media.index');
        Route::post('/media', [MediaController::class, 'store'])->name('media.store');
        Route::get('/enquiries', [App\Http\Controllers\Admin\EnquiryController::class, 'index'])->name('enquiries.index');
        Route::patch('/enquiries/{enquiry}', [App\Http\Controllers\Admin\EnquiryController::class, 'update'])->name('enquiries.update');
        Route::view('/account', 'admin.account')->name('account');
        Route::put('/account/password', [UserController::class, 'password'])->name('password');
        Route::put('/account/profile', [UserController::class, 'profile'])->name('profile');
        Route::middleware('can:manage-users')->group(function (): void {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::put('/users/{user}/password', [UserController::class, 'resetPassword'])->name('users.password');
        });
    });
});
