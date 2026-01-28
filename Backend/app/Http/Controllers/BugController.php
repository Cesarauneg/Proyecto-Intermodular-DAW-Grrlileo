<?php
namespace App\Http\Controllers;

use App\Models\Bug;
use Illuminate\Http\Request;

class BugController extends Controller
{
    //Devuelve todos los bugs
    public function index()
    {
        return response()->json(Bug::all());
    }

    //Filtrado dinámico por rarity y location (se pueden agregar mas)
    public function filter(Request $request)
    {
        $query = Bug::query();

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

    //Bugs disponibles en este momento (hemisferio norte y sur)
    public function available(Request $request)
    {
        $hemisphere = $request->get('hemisphere', 'north');

        return response()->json(
            Bug::availableNow($hemisphere)->get()
        );
    }
}
