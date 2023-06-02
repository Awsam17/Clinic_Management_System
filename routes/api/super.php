<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'super'
], function ($router) {
    Route::get('/statistics', [DashboardController::class, 'statistics']);
    Route::get('/doctors', [DashboardController::class, 'getDoctors']);
    Route::get('/clinics', [DashboardController::class, 'getClinics']);
    Route::get('/users', [DashboardController::class, 'getUsers']);
});
