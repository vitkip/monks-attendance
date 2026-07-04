<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
});

// Authenticated users
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('absences.index'));

    // Staff + Admin: record absences
    Route::get('/absences', fn() => view('absences.index'))->name('absences.index');

    // Staff + Admin: outstanding balance summary
    Route::get('/balance', fn() => view('balance.index'))->name('balance.index');

    // Custom Webpage Design (Donezo Dashboard from Stitch)
    Route::get('/custom-webpage-design', fn() => view('designs.custom-webpage-design'))->name('custom-webpage-design');

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::get('/monks', fn() => view('monks.index'))->name('monks.index');
        Route::get('/fine-rates', fn() => view('fine-rates.index'))->name('fine-rates.index');
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
