<?php

use App\Models\Organization;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/example', function () {
    $test = Organization::with(['building', 'activities'])->get();
    return $test;
});
