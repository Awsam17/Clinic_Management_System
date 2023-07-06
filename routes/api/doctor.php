<?php

use App\Http\Controllers\DoctorController;
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
    'prefix' => 'doctor'
], function ($router) {
    Route::post('/apply', [DoctorController::class, 'apply']);
    Route::get('/profile', [DoctorController::class, 'doctor_profile']);
    Route::post('/edit_profile', [DoctorController::class, 'doctor_edit']);
});

