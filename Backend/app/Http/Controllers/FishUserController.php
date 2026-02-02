<?php

namespace App\Http\Controllers;
use App\Models\Fish;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class FishUserController extends Controller
{
    // Añadir un pez a un usuario (al donarlo al museo)
public function donate($fishId)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'No autenticado'], 401);
    }

    $user->fish()->toggle([$fishId => ['donated_to_museum' => true]]);

    return response()->json(['success' => true], 200);
}
    // Listar los peces de un usuario
    public function index()
    {
        $user = Auth::user();
        $fish = $user->fish()->get();   

        return response()->json($fish);
    }   

}