<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AttendeeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Auth Routes (User Management)
Route::group(['prefix' => 'auth'], function () {
    // Public Routes (Bisa diakses siapa saja)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Routes (Harus pakai Token)
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
    });
});

// 2. Resource Routes (Semua di bawah ini butuh Token)
Route::middleware(['auth:api'])->group(function () {
    
    // Events & Tickets
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    
    Route::post('/events/{id}/tickets', [TicketController::class, 'store']);
    Route::get('/events/{id}/tickets', [TicketController::class, 'index']);

    // Transactions (Dengan Logging Activity)
    Route::middleware('activity.log')->group(function () {
        Route::post('/transactions', [TransactionController::class, 'store']);
    });
    Route::get('/transactions/{code}', [TransactionController::class, 'show']);
    
    // Attendees (Dengan Logging Activity)
    Route::middleware('activity.log')->group(function () {
        Route::post('/transactions/{id}/attendees', [AttendeeController::class, 'store']);
    });
    Route::get('/events/{id}/attendees', [AttendeeController::class, 'getByEvent']);
});