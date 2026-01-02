<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/', [DashboardController::class, 'index']);

Route::get('/heatmap', [DashboardController::class, 'heatmap'])
    ->name('heatmap');
