<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;

class CollectionController extends Controller
{

    public function getPurchasedComics(Request $request)
    {
        $perPage = $request->input('perPage', 8);
        $purchasedComics = Collection::with('comic')
            ->whereNull('rental_end_date') // Filter for purchased comics
            ->paginate($perPage);

        return response()->json([
            'data' => $purchasedComics->items(),
            'total' => $purchasedComics->total(),
            'per_page' => $purchasedComics->perPage(),
            'current_page' => $purchasedComics->currentPage(),
        ]);
    }

    public function getRentedComics(Request $request)
    {
        $perPage = $request->input('perPage', 8);
        $rentedComics = Collection::with('comic')
            ->whereNotNull('rental_end_date') // Filter for rented comics
            ->paginate($perPage);

        return response()->json([
            'data' => $rentedComics->items(),
            'total' => $rentedComics->total(),
            'per_page' => $rentedComics->perPage(),
            'current_page' => $rentedComics->currentPage(),
        ]);
    }
}