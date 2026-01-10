<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController; // Pastikan di-import
use App\Http\Controllers\AttendeeController;    // Pastikan di-import

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Auth Routes
Route::group(['prefix' => 'auth'], function () {
    // Public (Bisa diakses tanpa token)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Private (Harus punya Token)
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
    });
});

// 2. Resource Routes (Semua harus login / pakai Token)
Route::middleware(['auth:api'])->group(function () {
    
    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);

    // Tickets
    Route::post('/events/{id}/tickets', [TicketController::class, 'store']);
    Route::get('/events/{id}/tickets', [TicketController::class, 'index']);

    // Transactions (Plus Activity Log untuk transaksi)
    Route::middleware('activity.log')->group(function () {
        Route::post('/transactions', [TransactionController::class, 'store']);
    });
    Route::get('/transactions/{code}', [TransactionController::class, 'show']);
    
    // Attendees
    Route::middleware('activity.log')->group(function () {
        Route::post('/transactions/{id}/attendees', [AttendeeController::class, 'store']);
    });
    Route::get('/events/{id}/attendees', [AttendeeController::class, 'getByEvent']);
});