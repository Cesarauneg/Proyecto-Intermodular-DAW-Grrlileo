<?php
namespace App\Http\Controllers;

use App\Models\Fish;
use Illuminate\Http\Request;

class FishController extends Controller
{
public function index(Request $request)
{
    $query = Fish::query();

    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where('name_es', 'LIKE', "%{$searchTerm}%")
              ->orWhere('location', 'LIKE', "%{$searchTerm}%");
    }

    // return response()->json($query->paginate($request->get('per_page', 8)));
    return response()->json($query->get());
}

    //Filtrado dinámico por rarity y location (se pueden agregar mas)
    public function filter(Request $request)
{
    $query = Fish::query();

    // Filtro por búsqueda de texto
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('name_es', 'LIKE', "%{$searchTerm}%")
              ->orWhere('location', 'LIKE', "%{$searchTerm}%");
        });
    }

    // Filtro por rareza
    if ($request->filled('rarity')) {
        $query->where('rarity', $request->rarity);
    }

    // Filtro por ubicación
    if ($request->filled('location')) {
        $query->where('location', $request->location);
    }

    // Ordenar por precio
    if ($request->has('price_order')) {
        $order = strtolower($request->price_order) === 'desc' ? 'desc' : 'asc';
        $query->orderBy('price', $order);
    }

    // return response()->json($query->paginate($request->get('per_page', 8)));
    return response()->json($query->get());
}

    //Peces disponibles en este momento (hemisferio norte y sur)
    public function available(Request $request)
    {
        $hemisphere = $request->get('hemisphere', 'north');

        return response()->json(
            Fish::availableNow($hemisphere)->get()
        );
    }
}
