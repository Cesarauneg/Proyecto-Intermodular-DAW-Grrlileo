<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fossil;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FossilUserController extends Controller
{
        // Añadir un fosil a un usuario (al donarlo al museo)
public function donate($fossilId)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'No autenticado'], 401);
    }

    $user->fossils()->toggle([$fossilId => ['donated_to_museum' => true]]);

    return response()->json(['success' => true], 200);
}
    // Listar los fosiles de un usuario
    public function index()
    {
        $user = Auth::user();
        $fossils = $user->fossils()->get();   

        return response()->json($fossils);
    } 
}
