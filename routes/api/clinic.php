<?php

use App\Http\Controllers\ClinicController;
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
    'prefix' => 'clinic'
], function ($router) {
    Route::get('/get_regions' , [ClinicController::class , 'getRegions']);
    Route::get('/profile', [ClinicController::class, 'profile']);
    Route::post('/approve_doctor', [ClinicController::class, 'approveDoctor']);
    Route::get('/applications', [ClinicController::class, 'applications']);
    Route::get('/requests', [ClinicController::class, 'requests']);
    Route::get('/doctors', [ClinicController::class, 'doctors']);
    Route::get('/patients', [ClinicController::class, 'patients']);
    Route::post('/delete_patient', [ClinicController::class, 'deletePatient']);
    Route::get('/overview', [ClinicController::class, 'statistics']);
    Route::get('/secretaries', [ClinicController::class, 'secretaries']);
    Route::post('/delete_secretary', [ClinicController::class, 'deleteSec']);
    Route::get('/approved_appointments', [ClinicController::class, 'ApprovedAppointments']);
    Route::post('/reject_doctor', [ClinicController::class, 'rejectDoctor']);
    Route::post('/reject_appointment', [ClinicController::class, 'rejectAppointment']);
    Route::post('/delete_appointment', [ClinicController::class, 'deleteAppointment']);
    Route::post('/approve_appointment', [ClinicController::class, 'appreveAppointment']);
    Route::get('/monthly_statistics', [ClinicController::class, 'monthlyStatistics']);
    Route::post('/archive_appointment' , [ClinicController::class , 'archiveApp']);
});
