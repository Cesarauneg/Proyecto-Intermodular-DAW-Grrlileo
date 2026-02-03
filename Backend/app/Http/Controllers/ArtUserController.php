<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ArtUserController extends Controller
{
   // Añadir un arte a un usuario (al donarlo al museo)
public function donate($artId)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'No autenticado'], 401);
    }

    $user->art()->toggle([$artId => ['donated_to_museum' => true]]);

    return response()->json(['success' => true], 200);
}
    // Listar las obras de arte de un usuario
    public function index()
    {
        $user = Auth::user();
        $art = $user->art()->get();   

        return response()->json($art);
    }   
}
