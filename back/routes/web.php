<?php

use App\Models\Trip;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    dd(Trip::find(100)->driver());
});

