<?php
use App\Http\Controllers\BugController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FishController;
use App\Http\Controllers\VillagerController;

//Rutas para listar y filtrar bichos.
Route::get('/bugs', [BugController::class, 'index']);
Route::get('/bugs/filter', [BugController::class, 'filter']);
Route::get('/bugs/available', [BugController::class, 'available']);

//Rutas para listar y filtrar peces.
Route::get('/fish', [FishController::class, 'index']);
Route::get('/fish/filter', [FishController::class, 'filter']);
Route::get('/fish/available', [FishController::class, 'available']);

//Rutas para listar y filtrar aldeanos.
Route::get('/villagers', [VillagerController::class, 'index']);
Route::get('/villagers/filter', [VillagerController::class, 'filter']);