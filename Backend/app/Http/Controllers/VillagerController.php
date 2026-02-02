<?php
namespace App\Http\Controllers;

use App\Models\Villager;
use Illuminate\Http\Request;

class VillagerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        return Villager::orderBy('name_es')->paginate($perPage);
    }

    //Filtrado dinámico por personalidad, especie, género y hobby (se pueden agregar mas)
    public function filter(Request $request)
    {
        $query = Villager::query();

        if ($request->filled('search')) {
            $query->where('name_es', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('personality')) {
            $query->where('personality', $request->personality);
        }

        if ($request->filled('species')) {
            $query->where('species', $request->species);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('hobby')) {
            $query->where('hobby', $request->hobby);
        }

        $perPage = $request->get('per_page', 20);
        return $query->orderBy('name_es')->paginate($perPage);
    }

    public function filters()
    {
        return response()->json([
            'personalities' => Villager::distinct()->pluck('personality')->filter()->values(),
            'species' => Villager::distinct()->pluck('species')->filter()->values(),
            'genders' => Villager::distinct()->pluck('gender')->filter()->values(),
            'hobbies' => Villager::distinct()->pluck('hobby')->filter()->values(),
        ]);
    }
}
