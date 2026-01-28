<?php
use App\Http\Controllers\BugController;
use Illuminate\Support\Facades\Route;

//Rutas para listar y filtrar bichos.
Route::get('/bugs', [BugController::class, 'index']);
Route::get('/bugs/filter', [BugController::class, 'filter']);
Route::get('/bugs/available', [BugController::class, 'available']);
