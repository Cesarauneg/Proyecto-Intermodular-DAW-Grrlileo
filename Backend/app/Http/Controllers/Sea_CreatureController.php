<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sea_Creature;

class Sea_CreatureController extends Controller
{
    // Devuelve todas las criaturas marinas
    public function index()
    {
        return response()->json(Sea_Creature::all());
    }

        //Sea_Creatures disponibles en este momento (hemisferio norte y sur)
    public function available(Request $request)
    {
        $hemisphere = $request->get('hemisphere', 'north');

        return response()->json(
            Sea_Creature::availableNow($hemisphere)->get()
        );
    }

    // Filtrado dinámico por speed y shadow, ordenamiento por precio
    public function filter(Request $request)
    {
        $query = Sea_Creature::query();

        if ($request->has('speed')) {
            $query->where('speed', $request->speed);
        }
        if ($request->has('shadow')) {
            $query->where('shadow', $request->shadow);
        }
        // Ordenar por precio
        if ($request->has('price_order')) {
            $order = strtolower($request->price_order) === 'desc' ? 'desc' : 'asc';
            $query->orderBy('price', $order);
        }

        return response()->json($query->get());
    }
}
