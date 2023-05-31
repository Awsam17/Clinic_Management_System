<?php

use App\Http\Controllers\ClinicController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
    'prefix' => 'user'
], function ($router) {
    Route::post('/login', [AuthController::class, 'userLogin']);
    Route::post('/register', [AuthController::class, 'userRegister']);
    Route::post('/continue_as_doctor', [AuthController::class, 'continueAsdoctor']);
    Route::post('/logout', [AuthController::class, 'userLogout']);
  //  Route::post('/refresh', [AuthController::class, 'refresh']);
    //Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'clinic'
], function ($router) {
    Route::post('/login', [AuthController::class, 'clinicLogin']);
    Route::post('/register', [AuthController::class, 'clinicRegister']);
    Route::post('/logout', [AuthController::class, 'clinicLogout']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'secretary'
], function ($router) {
    Route::post('/login', [AuthController::class, 'secretaryLogin']);
    Route::post('/register', [AuthController::class, 'secretaryRegister']);
    Route::post('/logout', [AuthController::class, 'secretaryLogout']);
});
