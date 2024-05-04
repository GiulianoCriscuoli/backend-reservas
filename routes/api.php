<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FoundandLostController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\UserController;
use App\Models\Reservation;

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function() {

    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Mural dea avisos

    Route::get('/walls', [WallController::class, 'getAll']);
    Route::post('/walls/{id}/like', [WallController::class, 'like']);

    // Documentos

    Route::get('docs', [DocController::class, 'getAll']);

    // Livro de ocorrências

    Route::get('/warnings', [WarningController::class, 'getMyWarnings']);
    Route::post('/warning', [WarningController::class, 'setWarnings']);
    Route::post('/warning/file', [WarningController::class, 'addWarningFile']);

    // Boletos

    Route::get('/billets', [BilletController::class, 'getAll']);

    // Achados e perdidos

    Route::post('/foundandlost', [FoundandLostController::class, 'insert']);
    Route::put('/foundandlost/{id}', [BilletController::class, 'update']);

    // Unidade

    Route::get('/unit/{id}', [UnitController::class, 'getInfo']);
    Route::post('/unit/{id}/addperson', [UnitController::class, 'addPerson']);
    Route::delete('/unit/{id}/removeperson', [UnitController::class, 'removePerson']);
    Route::post('/unit/{id}/addVehicle', [UnitController::class, 'addVehicle']);
    Route::delete('/unit/{id}/removeVehicle', [UnitController::class, 'removeVehicle']);
    Route::post('/unit/{id}/addPet', [UnitController::class, 'addPet']);
    Route::delete('/unit/{id}/removePet', [UnitController::class, 'removePet']);

    // Reservas

    Route::get('/reservations', [ReservationController::class, 'getReservations']);
    Route::get('/myreservations', [ReservationController::class, 'getMyReservations']);
    Route::delete('/myreservations/{id}', [ReservationController::class, 'deleteMyReservations']);
    Route::post('/myreservations/{id}', [ReservationController::class, 'setReservation']);
    Route::get('/reservations/{id}/disableddates', [ReservationController::class, 'getDisabledDates']);
    Route::get('/reservations/{id}/times', [ReservationController::class, 'getTimes']);

});
