<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardGuestController;
use App\Http\Controllers\DashboardWishController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\WishController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/alya-dan-salman');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'create'])->name('login');
    Route::post('/admin/login', [AuthController::class, 'store'])->middleware('throttle:admin-login')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/admin/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/invitations/{invitation}/wishes', [DashboardWishController::class, 'index'])->name('dashboard.wishes');
    Route::patch('/dashboard/invitations/{invitation}/wishes/{wish}', [DashboardWishController::class, 'update'])->name('dashboard.wishes.update');
    Route::patch('/dashboard/invitations/{invitation}/settings', [DashboardWishController::class, 'settings'])->name('dashboard.invitations.settings');
    Route::get('/dashboard/invitations/{invitation}/guests', [DashboardGuestController::class, 'index'])->name('dashboard.guests');
    Route::post('/dashboard/invitations/{invitation}/guests', [DashboardGuestController::class, 'store'])->name('dashboard.guests.store');
});

Route::post('/{invitation}/wishes', [WishController::class, 'store'])
    ->middleware('throttle:wishes')
    ->name('wishes.store');
Route::get('/{invitation}', [InvitationController::class, 'show'])->name('invitation.show');
