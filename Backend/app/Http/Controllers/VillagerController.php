<?php
namespace App\Http\Controllers;

use App\Models\Villager;
use Illuminate\Http\Request;

class VillagerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        return Villager::paginate($perPage);
    }

    //Filtrado dinámico por personalidad, especie, género y hobby (se pueden agregar mas)
    public function filter(Request $request)
    {
        $query = Villager::query();

        if ($request->has('personality')) {
            $query->where('personality', $request->personality);
        }

        if ($request->has('species')) {
            $query->where('species', $request->species);
        }

        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->has('hobby')) {
            $query->where('hobby', $request->hobby);
        }

        return response()->json($query->get());
    }
}
