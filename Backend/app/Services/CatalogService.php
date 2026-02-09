<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CatalogService
{
    public function getAll(string $modelClass, array $columns = ['*'], int $ttl = 3600): mixed
    {
        $key = 'catalog_' . class_basename($modelClass) . '_all';

        return Cache::remember($key, $ttl, fn() => $modelClass::select($columns)->get());
    }

    public function applyFilters(string $modelClass, Request $request, array $filterableColumns): Builder
    {
        $query = $modelClass::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name_es', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('name_en', 'LIKE', "%{$searchTerm}%");
            });
        }

        foreach ($filterableColumns as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        if ($request->filled('price_order')) {
            $order = strtolower($request->price_order) === 'desc' ? 'desc' : 'asc';
            $query->orderBy('price', $order);
        }

        return $query;
    }

    public function getAvailable(string $modelClass, string $hemisphere = 'north'): mixed
    {
        return $modelClass::availableNow($hemisphere)->get();
    }
}
