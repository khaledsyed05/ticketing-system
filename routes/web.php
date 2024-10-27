<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\web\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
        ->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
        ->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/create', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('/', [TicketController::class, 'store'])->name('tickets.store');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
        Route::get('/edit/{ticket}', [TicketController::class, 'edit'])->name('tickets.edit');
        Route::put('/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
        Route::put('/{ticket}/status', [TicketController::class, 'updateStatus']);
        Route::get('/status/{status_id}', [TicketController::class, 'getByStatus']);
        Route::get('/user/{user_id}', [TicketController::class, 'getByUser']);
    });
});
