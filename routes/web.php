<?php

use Illuminate\Support\Facades\Route;
use App\Models\Ticket;
Route::get('/', function () {
    return view('base_layout');
});

Route::get('/ticket-management', function () {
    return view('base_layout');
});

Route::post('/api/ticket/add', function () {
    return response("OK",200);
});