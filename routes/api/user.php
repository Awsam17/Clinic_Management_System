<?php

use App\Http\Controllers\UserController;
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
    'prefix' => 'user'
], function ($router) {
    Route::get('/home', [UserController::class, 'home']);
    Route::get('/clinics', [UserController::class, 'getClinics']);
    Route::get('/specialties', [UserController::class, 'getSpecialties']);
    Route::get('/specialty/doctors', [UserController::class, 'getSpecialtyDoctors']);
    Route::get('/clinic/doctors', [UserController::class, 'getClinicDoctors']);
    Route::get('/doctor/profile', [UserController::class, 'doctorProfile']);
    Route::get('/search_clinics', [UserController::class, 'searchClinics']);
    Route::get('/search_clinic_doctors', [UserController::class, 'searchClinicDoctors']);
    Route::get('/search_specialty_doctors', [UserController::class, 'searchSpecialtyDoctors']);
    Route::get('/doctor/profile_in_clinic', [UserController::class, 'doctorProfileInClinic']);
    Route::get('/doctor/available_times', [UserController::class, 'availableTimes']);
});

