<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;

Route::get('/', function () {
    return view('prompt');
});

Route::post('/prompt', [AIController::class, 'handlePrompt']);
