<?php

namespace App\Http\Controllers;

use App\Models\Villager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VillagerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        return Villager::orderBy('name_es')->paginate($perPage);
    }

    public function filter(Request $request)
    {
        $query = Villager::query();

        if ($request->filled('search')) {
            $query->where('name_es', 'like', '%' . $request->search . '%');
        }

        foreach (['personality', 'species', 'gender', 'hobby'] as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        $perPage = $request->get('per_page', 20);

        return $query->orderBy('name_es')->paginate($perPage);
    }

    public function filters()
    {
        return response()->json(Cache::remember('villager_filters', 86400, fn() => [
            'personalities' => Villager::distinct()->pluck('personality')->filter()->values(),
            'species'       => Villager::distinct()->pluck('species')->filter()->values(),
            'genders'       => Villager::distinct()->pluck('gender')->filter()->values(),
            'hobbies'       => Villager::distinct()->pluck('hobby')->filter()->values(),
        ]));
    }
}
