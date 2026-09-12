<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ClearCacheController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\WizardAvatarController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'landing'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::post('/cache/clear', ClearCacheController::class)->name('cache.clear');
    Route::get('/impersonation/enter/{user}', [ImpersonationController::class, 'enter'])
        ->name('impersonation.enter');
    Route::get('/impersonation/leave', [ImpersonationController::class, 'leave'])
        ->name('impersonation.leave');
    Route::get('/preview-avatar', [WizardAvatarController::class, 'show'])
        ->name('studio.preview-avatar');
});

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}(?:-[a-z]{2})?'])
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('{username}', [PortfolioController::class, 'show'])->name('portfolio.show');
        Route::get('{username}/cv', [PortfolioController::class, 'cv'])->name('portfolio.cv');
        Route::get('{username}/cv.pdf', [PortfolioController::class, 'cvPdf'])->name('portfolio.cv.pdf');
    });
