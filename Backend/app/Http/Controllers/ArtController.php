<?php

namespace App\Http\Controllers;


use App\Models\Art;
use Illuminate\Http\Request;

class ArtController extends Controller
{
    // Devuelve todas las obras de arte
    public function index()
    {
        return response()->json(Art::all());
    }

        //Filtrado dinámico por personalidad, especie, género y hobby (se pueden agregar mas)
    public function filter(Request $request)
    {
        $query = Art::query();

        if ($request->has('has_fake')) {
            $query->where('has_fake', $request->has_fake);
        }

        return response()->json($query->get());
    }
}
