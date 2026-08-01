<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'store'])->name('login.store');
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
    Route::get('/absences', [\App\Http\Controllers\AbsenceController::class, 'index'])->name('absences.index');
    Route::post('/absences', [\App\Http\Controllers\AbsenceController::class, 'store'])->name('absences.store');
    Route::put('/absences/{absence}', [\App\Http\Controllers\AbsenceController::class, 'update'])->name('absences.update');
    Route::patch('/absences/{absence}/mark-paid', [\App\Http\Controllers\AbsenceController::class, 'markPaid'])->name('absences.mark-paid');
    Route::delete('/absences/{absence}', [\App\Http\Controllers\AbsenceController::class, 'destroy'])->name('absences.destroy');

    // Staff + Admin: outstanding balance summary
    Route::get('/balance', [\App\Http\Controllers\BalanceController::class, 'index'])->name('balance.index');
    Route::patch('/balance/{monk}/mark-all-paid', [\App\Http\Controllers\BalanceController::class, 'markAllPaid'])->name('balance.mark-all-paid');

    // Staff + Admin: view news; Admin only can manage (see below)
    Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news.index');

    // Staff + Admin: view chants; Admin only can manage (see below)
    Route::get('/chants', [\App\Http\Controllers\ChantController::class, 'index'])->name('chants.index');

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
        Route::get('/electricity-bills', [\App\Http\Controllers\ElectricityBillController::class, 'index'])->name('electricity-bills.index');
        Route::post('/electricity-bills', [\App\Http\Controllers\ElectricityBillController::class, 'store'])->name('electricity-bills.store');
        Route::post('/electricity-bills/{electricityBill}/update', [\App\Http\Controllers\ElectricityBillController::class, 'update'])->name('electricity-bills.update');
        Route::delete('/electricity-bills/{electricityBill}', [\App\Http\Controllers\ElectricityBillController::class, 'destroy'])->name('electricity-bills.destroy');

        // Construction projects / ໂຄງການກໍ່ສ້າງ (ລາຍຈ່າຍ/ລາຍຮັບ ແລະ ສະຫຼຸບ)
        Route::get('/construction-projects', [\App\Http\Controllers\ConstructionProjectController::class, 'index'])->name('construction-projects.index');
        Route::post('/construction-projects', [\App\Http\Controllers\ConstructionProjectController::class, 'store'])->name('construction-projects.store');
        Route::get('/construction-projects/{constructionProject}', [\App\Http\Controllers\ConstructionProjectController::class, 'show'])->name('construction-projects.show');
        Route::put('/construction-projects/{constructionProject}', [\App\Http\Controllers\ConstructionProjectController::class, 'update'])->name('construction-projects.update');
        Route::delete('/construction-projects/{constructionProject}', [\App\Http\Controllers\ConstructionProjectController::class, 'destroy'])->name('construction-projects.destroy');
        Route::post('/construction-projects/{constructionProject}/transactions', [\App\Http\Controllers\ConstructionProjectController::class, 'storeTransaction'])->name('construction-projects.transactions.store');
        Route::put('/construction-projects/{constructionProject}/transactions/{transaction}', [\App\Http\Controllers\ConstructionProjectController::class, 'updateTransaction'])->name('construction-projects.transactions.update');
        Route::delete('/construction-projects/{constructionProject}/transactions/{transaction}', [\App\Http\Controllers\ConstructionProjectController::class, 'destroyTransaction'])->name('construction-projects.transactions.destroy');

        // News & announcements
        Route::post('/news', [\App\Http\Controllers\NewsController::class, 'store'])->name('news.store');
        Route::post('/news/{news}/update', [\App\Http\Controllers\NewsController::class, 'update'])->name('news.update');
        Route::delete('/news/{news}', [\App\Http\Controllers\NewsController::class, 'destroy'])->name('news.destroy');
        Route::patch('/news/{news}/toggle-publish', [\App\Http\Controllers\NewsController::class, 'togglePublish'])->name('news.toggle-publish');

        // Chants / ບົດສູດມົນ
        Route::post('/chants', [\App\Http\Controllers\ChantController::class, 'store'])->name('chants.store');
        Route::put('/chants/{chant}', [\App\Http\Controllers\ChantController::class, 'update'])->name('chants.update');
        Route::delete('/chants/{chant}', [\App\Http\Controllers\ChantController::class, 'destroy'])->name('chants.destroy');

        Route::get('/monks', [\App\Http\Controllers\MonkController::class, 'index'])->name('monks.index');
        Route::post('/monks', [\App\Http\Controllers\MonkController::class, 'store'])->name('monks.store');
        Route::post('/monks/{monk}/update', [\App\Http\Controllers\MonkController::class, 'update'])->name('monks.update');
        Route::delete('/monks/{monk}', [\App\Http\Controllers\MonkController::class, 'destroy'])->name('monks.destroy');
        Route::get('/fine-rates', [\App\Http\Controllers\FineRateController::class, 'index'])->name('fine-rates.index');
        Route::post('/fine-rates', [\App\Http\Controllers\FineRateController::class, 'store'])->name('fine-rates.store');
        Route::put('/fine-rates/{fineRate}', [\App\Http\Controllers\FineRateController::class, 'update'])->name('fine-rates.update');
        Route::delete('/fine-rates/{fineRate}', [\App\Http\Controllers\FineRateController::class, 'destroy'])->name('fine-rates.destroy');
        Route::get('/hero-slides', [\App\Http\Controllers\HeroSlideController::class, 'index'])->name('hero-slides.index');
        Route::post('/hero-slides', [\App\Http\Controllers\HeroSlideController::class, 'store'])->name('hero-slides.store');
        Route::post('/hero-slides/{heroSlide}/update', [\App\Http\Controllers\HeroSlideController::class, 'update'])->name('hero-slides.update');
        Route::patch('/hero-slides/{heroSlide}/toggle-publish', [\App\Http\Controllers\HeroSlideController::class, 'togglePublish'])->name('hero-slides.toggle-publish');
        Route::patch('/hero-slides/{heroSlide}/move-up', [\App\Http\Controllers\HeroSlideController::class, 'moveUp'])->name('hero-slides.move-up');
        Route::patch('/hero-slides/{heroSlide}/move-down', [\App\Http\Controllers\HeroSlideController::class, 'moveDown'])->name('hero-slides.move-down');
        Route::delete('/hero-slides/{heroSlide}', [\App\Http\Controllers\HeroSlideController::class, 'destroy'])->name('hero-slides.destroy');
        Route::get('/news-categories', [\App\Http\Controllers\NewsCategoryController::class, 'index'])->name('news-categories.index');
        Route::post('/news-categories', [\App\Http\Controllers\NewsCategoryController::class, 'store'])->name('news-categories.store');
        Route::put('/news-categories/{newsCategory}', [\App\Http\Controllers\NewsCategoryController::class, 'update'])->name('news-categories.update');
        Route::delete('/news-categories/{newsCategory}', [\App\Http\Controllers\NewsCategoryController::class, 'destroy'])->name('news-categories.destroy');
        Route::get('/chant-categories', [\App\Http\Controllers\ChantCategoryController::class, 'index'])->name('chant-categories.index');
        Route::post('/chant-categories', [\App\Http\Controllers\ChantCategoryController::class, 'store'])->name('chant-categories.store');
        Route::put('/chant-categories/{chantCategory}', [\App\Http\Controllers\ChantCategoryController::class, 'update'])->name('chant-categories.update');
        Route::delete('/chant-categories/{chantCategory}', [\App\Http\Controllers\ChantCategoryController::class, 'destroy'])->name('chant-categories.destroy');
        Route::get('/duty-schedules', [\App\Http\Controllers\DutyScheduleController::class, 'index'])->name('duty-schedules.index');
        Route::post('/duty-schedules', [\App\Http\Controllers\DutyScheduleController::class, 'store'])->name('duty-schedules.store');
        Route::put('/duty-schedules/{dutySchedule}', [\App\Http\Controllers\DutyScheduleController::class, 'update'])->name('duty-schedules.update');
        Route::delete('/duty-schedules/{dutySchedule}', [\App\Http\Controllers\DutyScheduleController::class, 'destroy'])->name('duty-schedules.destroy');
        Route::get('/duty-schedules/report', \App\Http\Controllers\DutyScheduleReportController::class)->name('duty-schedules.report');
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/logo', [\App\Http\Controllers\SettingController::class, 'updateLogo'])->name('settings.logo.update');
        Route::post('/settings/logo/remove', [\App\Http\Controllers\SettingController::class, 'removeLogo'])->name('settings.logo.remove');
        Route::post('/settings/contact', [\App\Http\Controllers\SettingController::class, 'updateContact'])->name('settings.contact.update');
    });

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/info', [\App\Http\Controllers\ProfileController::class, 'updateInfo'])->name('profile.update-info');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

});
