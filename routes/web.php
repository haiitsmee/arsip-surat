<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\AuthController;


// Dashboard (simple redirect to letters index) — used by layout nav
use App\Http\Controllers\DashboardController;

// Authentication (simple)
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

	// Resource routes for letters
	Route::resource('letters', LetterController::class);

	// Separate download route (resource doesn't include download)
	Route::get('letters/{letter}/download', [LetterController::class, 'download'])->name('letters.download');
});
