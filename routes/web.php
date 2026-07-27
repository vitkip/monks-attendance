<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
});

// Public news (no login required — visible to the community). Home page for guests;
// authenticated users are redirected to their dashboard.
Route::get('/', function (\Illuminate\Http\Request $request) {
    if (Auth::check()) {
        return redirect()->route('absences.index');
    }

    return app(\App\Http\Controllers\PublicNewsController::class)->index($request);
})->name('news.public.index');
// Public pages (no login required). Deliberately NOT prefixed "/public" — on
// hosts where the document root is the project root (not public/), Laravel's
// own front controller lives at /public/index.php, so a route path that also
// starts with "/public" gets its prefix silently stripped as the app's base
// path and collides with the same-named admin route (e.g. /public/monks
// would resolve internally to /monks and hit the admin-only page instead).
Route::prefix('info')->group(function () {
    Route::get('/news/{slug}', [\App\Http\Controllers\PublicNewsController::class, 'show'])->name('news.public.show');

    // Public monks & novices directory (no login required)
    Route::get('/monks', [\App\Http\Controllers\PublicMonkController::class, 'index'])->name('monks.public.index');

    // Public chants / ບົດສູດມົນ (no login required — visible to the community)
    Route::get('/chants', [\App\Http\Controllers\PublicChantController::class, 'index'])->name('chants.public.index');
    Route::get('/chants/{slug}', [\App\Http\Controllers\PublicChantController::class, 'show'])->name('chants.public.show');

    // Public electricity bill transparency / ລາຍຈ່າຍຄ່າໄຟຟ້າ (no login required)
    Route::get('/electricity-bills', [\App\Http\Controllers\PublicElectricityBillController::class, 'index'])->name('electricity-bills.public.index');

    // Public construction project transparency / ໂຄງການກໍ່ສ້າງ (no login required)
    Route::get('/construction-projects', [\App\Http\Controllers\PublicConstructionProjectController::class, 'index'])->name('construction-projects.public.index');
    Route::get('/construction-projects/{project}', [\App\Http\Controllers\PublicConstructionProjectController::class, 'show'])->name('construction-projects.public.show');
});

// Authenticated users
Route::middleware('auth')->group(function () {

    // Staff + Admin: record absences
    Route::get('/absences', fn() => view('absences.index'))->name('absences.index');

    // Staff + Admin: outstanding balance summary
    Route::get('/balance', fn() => view('balance.index'))->name('balance.index');

    // Custom Webpage Design (Donezo Dashboard from Stitch)
    Route::get('/custom-webpage-design', fn() => view('designs.custom-webpage-design'))->name('custom-webpage-design');

    // Temple Management System (TMS) Designs from Stitch
    Route::prefix('designs/tms')->group(function () {
        Route::get('/dashboard', fn() => view('designs.tms-dashboard'))->name('designs.tms-dashboard');
        Route::get('/monk-management', fn() => view('designs.tms-monk-management'))->name('designs.tms-monk-management');
        Route::get('/public-news', fn() => view('designs.tms-public-news'))->name('designs.tms-public-news');
        Route::get('/news-announcement', fn() => view('designs.tms-news-announcement'))->name('designs.tms-news-announcement');
    });

    // Admin only
    Route::middleware('admin')->group(function () {
        // Electricity bill notifications / ແຈ້ງບິນຄ່າໄຟຟ້າ
        Route::get('/electricity-bills', fn() => view('electricity-bills.index'))->name('electricity-bills.index');

        // Construction projects / ໂຄງການກໍ່ສ້າງ (ລາຍຈ່າຍ/ລາຍຮັບ ແລະ ສະຫຼຸບ)
        Route::get('/construction-projects', fn() => view('construction-projects.index'))->name('construction-projects.index');

        // News & announcements
        Route::get('/news', fn() => view('news.index'))->name('news.index');

        // Chants / ບົດສູດມົນ
        Route::get('/chants', fn() => view('chants.index'))->name('chants.index');

        Route::get('/monks', fn() => view('monks.index'))->name('monks.index');
        Route::get('/fine-rates', fn() => view('fine-rates.index'))->name('fine-rates.index');
        Route::get('/hero-slides', fn() => view('hero-slides.index'))->name('hero-slides.index');
        Route::get('/news-categories', fn() => view('news-categories.index'))->name('news-categories.index');
        Route::get('/chant-categories', fn() => view('chant-categories.index'))->name('chant-categories.index');
        Route::get('/duty-schedules', fn() => view('duty-schedules.index'))->name('duty-schedules.index');
        Route::get('/duty-schedules/report', \App\Http\Controllers\DutyScheduleReportController::class)->name('duty-schedules.report');
        Route::get('/users', fn() => view('users.index'))->name('users.index');
        Route::get('/settings', fn() => view('settings.index'))->name('settings.index');
    });

    // Profile
    Route::get('/profile', fn() => view('profile.show'))->name('profile.show');

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

});
