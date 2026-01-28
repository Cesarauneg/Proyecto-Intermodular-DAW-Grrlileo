<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fish;

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

