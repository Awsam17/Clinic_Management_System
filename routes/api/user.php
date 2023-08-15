<?php

use App\Http\Controllers\ChatController;
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
    Route::get('/clinic/profile', [UserController::class, 'clinicProfile']);
    Route::get('/search_clinics', [UserController::class, 'searchClinics']);
    Route::get('/search_clinic_doctors', [UserController::class, 'searchClinicDoctors']);
    Route::get('/search_specialty_doctors', [UserController::class, 'searchSpecialtyDoctors']);
    Route::get('/doctor/profile_in_clinic', [UserController::class, 'doctorProfileInClinic']);
    Route::get('/doctor/available_times', [UserController::class, 'availableTimes']);
    Route::get('/incoming_apps', [UserController::class, 'incomingApps']);
    Route::get('/booked_apps', [UserController::class, 'bookedApps']);
    Route::get('/archived_apps', [UserController::class, 'archivedApps']);
    Route::post('/make_app', [UserController::class, 'makeApp']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/edit_profile', [UserController::class, 'edit']);
    Route::get('/notifications', [UserController::class, 'notifications']);
    Route::get('/chats', [ChatController::class, 'getChats']);
});

