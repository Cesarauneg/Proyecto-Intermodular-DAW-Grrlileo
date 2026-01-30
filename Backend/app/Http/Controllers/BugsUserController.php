<?php
namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BugsUserController extends Controller
{
    // Añadir un insecto a un usuario (al donarlo al museo)
    public function donate($bugId)
    {
        $user = Auth::user();

        // Si no lo tiene aún → lo añadimos como donado
        if (! $user->bugs()->where('bugs_id', $bugId)->exists()) {
            $user->bugs()->attach($bugId, [
                'donated' => true,
            ]);
        } else {
            // Si ya lo tiene → solo actualizar pivot
            $user->bugs()->updateExistingPivot($bugId, [
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
        $bugs = $user->bugs()->get();

        return response()->json($bugs);
    }
}
