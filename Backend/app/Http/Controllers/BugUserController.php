<?php
namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BugUserController extends Controller
{
    // Añadir un insecto a un usuario (al donarlo al museo)
public function donate($bugId)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'No autenticado'], 401);
    }

    // Usamos el nombre exacto de tu modelo: donated_to_museum
    $user->bugs()->toggle([$bugId => ['donated_to_museum' => true]]);

    return response()->json(['success' => true], 200);
}
    // Listar los peces de un usuario
    public function index()
    {
        $user = Auth::user();
        $bugs = $user->bugs()->get();

        return response()->json($bugs);
    }
}
