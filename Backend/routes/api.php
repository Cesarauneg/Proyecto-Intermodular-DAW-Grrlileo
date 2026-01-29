<?php
use App\Http\Controllers\BugController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FishController;
use App\Http\Controllers\VillagerController;
use App\Http\Controllers\ArtController;
use App\Http\Controllers\FossilController;
use App\Http\Controllers\Sea_CreatureController;

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
Route::get('/villagers/filters', [VillagerController::class, 'filters']);

//Rutas para listar y filtrar obras de arte.
Route::get('/art', [ArtController::class, 'index']);
Route::get('/art/filter', [ArtController::class, 'filter']);

//Rutas para listar y filtrar fósiles.
Route::get('/fossils', [FossilController::class, 'index']);
Route::get('/fossils/filter', [FossilController::class, 'filter']);

//Rutas para listar y filtrar criaturas marinas.
Route::get('/sea_creatures', [Sea_CreatureController::class, 'index']);
Route::get('/sea_creatures/filter', [Sea_CreatureController::class, 'filter']);
Route::get('/sea_creatures/available', [Sea_CreatureController::class, 'available']);
