<?php
namespace App\Http\Controllers;

use App\Models\Fish;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FishUserController extends Controller
{
    // Añadir un pez a un usuario (al donarlo al museo)
    public function donate($fishId)
    {
        $user = Auth::user();

        // Si no lo tiene aún → lo añadimos como donado
        if (! $user->fish()->where('fish_id', $fishId)->exists()) {
            $user->fish()->attach($fishId, [
                'donated' => true,
            ]);
        } else {
            // Si ya lo tiene → solo actualizar pivot
            $user->fish()->updateExistingPivot($fishId, [
                'donated' => true,
            ]);
        }

        return response()->json([
            'ok'      => true,
            'donated' => true,
        ]);
    }
    // Listar los peces de un usuario
    public function index()
    {
        $user = Auth::user();
        $fish = $user->fish()->get();

        return response()->json($fish);
    }

}
