<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;

Route::post('/prompt', [AIController::class, 'handlePrompt']);
