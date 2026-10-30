<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

Route::prefix('v1')
    ->middleware([])
    ->group(function () {

    Route::prefix('ticket')->controller(TicketController::class)->group(function () {
        Route::get('/all', 'get');

    });

    // Route::get('/tickets', [TicketController::class, 'get']);
    // Route::post('/tickets', [TicketController::class, 'store']);
    // Route::get('/users/{id}', [UserController::class, 'show']);
});