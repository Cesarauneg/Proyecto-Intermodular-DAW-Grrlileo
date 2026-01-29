<?php
namespace App\Http\Controllers;

use App\Models\Fossil;
use Illuminate\Http\Request;

class FossilController extends Controller
{
    // Devuelve todos los fósiles
    public function index()
    {
        return response()->json(Fossil::all());
    }

    //Filtrado dinámico por part_of y ordenamiento por precio
    public function filter(Request $request)
    {
        $query = Fossil::query();

        if ($request->has('part_of')) {
            $query->where('part_of', $request->part_of);
        }

        // Ordenar por precio
        // Espera que el cliente envíe 'price_order=asc' o 'price_order=desc'
        if ($request->has('price_order')) {
            $order = strtolower($request->price_order) === 'desc' ? 'desc' : 'asc';
            $query->orderBy('price', $order);
        }

        return response()->json($query->get());
    }

}
