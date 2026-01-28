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

        return response()->json($query->get());
    }

    //Bugs disponibles en este momento (hemisferio norte y sur)
    public function available(Request $request)
    {
        $month      = now()->month;
        $hour       = now()->hour;
        $hemisphere = $request->get('hemisphere', 'north');

        $monthField = $hemisphere === 'south'
            ? 'month_array_southern'
            : 'month_array_northern';

        $bugs = Bug::whereJsonContains($monthField, $month)
            ->where(function ($q) use ($hour) {
                $q->whereJsonContains('time_array', $hour)
                    ->orWhere('is_all_day', true);
            })
            ->get();

        return response()->json($bugs);
    }
}
