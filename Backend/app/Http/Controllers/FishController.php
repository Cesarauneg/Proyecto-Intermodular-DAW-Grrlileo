<?php
namespace App\Http\Controllers;

use App\Models\Fish;
use Illuminate\Http\Request;

class FishController extends Controller
{
    public function index()
    {
        return response()->json([Fish::all()]);
    }

    //Filtrado dinámico por rarity y location (se pueden agregar mas)
    public function filter(Request $request)
    {
        $query = Fish::query();

        if ($request->has('rarity')) {
            $query->where('rarity', $request->rarity);
        }

        if ($request->has('location')) {
            $query->where('location', $request->location);
        }

        // Ordenar por precio
        if ($request->has('price_order')) {
            $order = strtolower($request->price_order) === 'desc' ? 'desc' : 'asc';
            $query->orderBy('price', $order);
        }

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
