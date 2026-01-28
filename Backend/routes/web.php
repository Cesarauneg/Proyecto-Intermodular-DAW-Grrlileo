<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [//en lugar de devolver una vista blade, renderiza el componente welcome.vue(inertia)
        //array de props que se pasan a welcome.vue
        //variables booleanas-> verifican si las rutas existen(permite que la vista muestre o no los enlaces de login/registro)
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';// En lugar de saturar web.php con todas las rutas de autenticación, estas se definen en un archivo separado (routes/auth.php) y se "importan" aquí.
