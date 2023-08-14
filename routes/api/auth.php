<?php

use App\Http\Controllers\ClinicController;
use App\Http\Controllers\DashboardController;
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
    Route::post('/forgot_password', [AuthController::class, 'userForgotPassword']);
    Route::post('/check_code', [AuthController::class, 'userCheckCode']);
    Route::post('/reset_password', [AuthController::class, 'userResetPassword']);
    Route::post('/request_verify', [AuthController::class, 'userRequestVerify']);
    Route::post('/verify', [AuthController::class, 'userVerify']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'clinic'
], function ($router) {
    Route::post('/login', [AuthController::class, 'clinicLogin']);
    Route::post('/register', [AuthController::class, 'clinicRegister']);
    Route::post('/logout', [AuthController::class, 'clinicLogout']);
    Route::post('/forgot_password', [AuthController::class, 'clinicForgotPassword']);
    Route::post('/check_code', [AuthController::class, 'clinicCheckCode']);
    Route::post('/reset_password', [AuthController::class, 'clinicResetPassword']);
    Route::post('/request_verify', [AuthController::class, 'clinicRequestVerify']);
    Route::post('/verify', [AuthController::class, 'clinicVerify']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'secretary'
], function ($router) {
    Route::post('/login', [AuthController::class, 'secretaryLogin']);
    Route::post('/register', [AuthController::class, 'secretaryRegister']);
    Route::post('/logout', [AuthController::class, 'secretaryLogout']);
    Route::post('/forgot_password', [AuthController::class, 'secretaryForgotPassword']);
    Route::post('/check_code', [AuthController::class, 'secretaryCheckCode']);
    Route::post('/reset_password', [AuthController::class, 'secretaryResetPassword']);
    Route::post('/request_verify', [AuthController::class, 'secretaryRequestVerify']);
    Route::post('/verify', [AuthController::class, 'secretaryVerify']);
});

Route::post('/test' , [ClinicController::class , 'archiveApp']);

