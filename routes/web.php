<?php

use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Livewire\Admin\Auth\ForgotPassword;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Auth\SetNewPassword;
use App\Livewire\Admin\Auth\VerifyCode;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest-only authentication screens.
    Route::middleware('guest')->group(function () {
        Route::get('login', Login::class)->name('login');
        Route::get('forgot-password', ForgotPassword::class)->name('password.request');
        Route::get('verify-code', VerifyCode::class)->name('password.verify');
        Route::get('set-password', SetNewPassword::class)->name('password.set');
    });

    // Authenticated admin portal.
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::view('/', 'admin.dashboard')->name('dashboard');
        Route::post('logout', LogoutController::class)->name('logout');
    });
});
