<?php

namespace App\Http\Controllers;
use App\Models\Villager;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class VillagerUserController extends Controller
{
    // Añadir un villager a favorito de un usuario
public function favorite($villagerId)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'No autenticado'], 401);
    }

    $user->villager()->toggle([$villagerId => ['is_favorite' => true]]);

    return response()->json(['success' => true], 200);
}
    // Listar los villagers favoritos de un usuario
    public function index()
    {
        $user = Auth::user();
        $villagers = $user->villager()->get();   

        return response()->json($villagers);
    }   

}