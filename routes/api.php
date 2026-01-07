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

// Resource 1: Users (Auth)
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
});

// Resource 2: Events
Route::group(['middleware' => 'api'], function () {
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
});

// Resource 3: Tickets
Route::group(['middleware' => 'api'], function () {
    Route::post('/events/{id}/tickets', [TicketController::class, 'store']);
    Route::get('/events/{id}/tickets', [TicketController::class, 'index']);
});

// Resource 4: Transactions (Dilindungi Auth & Log Activity)
Route::group(['middleware' => ['api', 'activity.log']], function () {
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{code}', [TransactionController::class, 'show']);
    Route::post('/transactions/{id}/attendees', [AttendeeController::class, 'store']);
});

// Resource 5: Attendees List (Admin View)
Route::group(['middleware' => 'api'], function () {
    Route::get('/events/{id}/attendees', [AttendeeController::class, 'getByEvent']);
});