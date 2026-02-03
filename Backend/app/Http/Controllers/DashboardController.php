<?php

namespace App\Http\Controllers;

use App\Models\Fish;
use App\Models\Bug;
use App\Models\Art;
use App\Models\Fossil;
use App\Models\Sea_Creature;
use Illuminate\Http\Request;
use Inertia\Inertia; 

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'peces'     => $user->fish()->where('donated_to_museum', true)->count(),
            'bichos'    => $user->bugs()->where('donated_to_museum', true)->count(),
            'arte'      => $user->art()->where('donated_to_museum', true)->count(),
            'fosiles'   => $user->fossils()->where('donated_to_museum', true)->count(),
            'criaturas' => $user->seaCreatures()->where('donated_to_museum', true)->count(),
        ];

        $totalesMaximos = [
            'peces'     => 80, 
            'bichos'    => 80, 
            'arte'      => 43, 
            'fosiles'   => 73, 
            'criaturas' => 40
        ];

return Inertia::render('Dashboard', [
    'stats' => $stats,  
    'maximos' => $totalesMaximos
]);
    }
}