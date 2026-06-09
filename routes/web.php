<?php

use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Livewire\Admin\Auth\ForgotPassword;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Auth\ResetSuccess;
use App\Livewire\Admin\Auth\SetNewPassword;
use App\Livewire\Admin\Auth\VerifyCode;
use Illuminate\Support\Facades\Route;

// Public website homepage.
Route::view('/', 'welcome')->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest-only authentication screens.
    Route::middleware('guest')->group(function () {
        Route::get('login', Login::class)->name('login');
        Route::get('forgot-password', ForgotPassword::class)->name('password.request');
        Route::get('verify-code', VerifyCode::class)->name('password.verify');
        Route::get('set-password', SetNewPassword::class)->name('password.set');
        Route::get('password-reset-success', ResetSuccess::class)->name('password.success');
    });

    // Authenticated admin portal.
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::view('/', 'admin.dashboard')->name('dashboard');
        Route::post('logout', LogoutController::class)->name('logout');
    });
});
