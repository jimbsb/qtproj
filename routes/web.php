<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('base.base_layout');
});
