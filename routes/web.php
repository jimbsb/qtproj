<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('base_layout');
});

Route::get('/ticket-management', function () {
    return view('base_layout');
});
