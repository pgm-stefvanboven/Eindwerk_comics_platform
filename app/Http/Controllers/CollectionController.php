<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 8); // Default to 8 comics per page
        $collections = Collection::with('comic')->paginate($perPage);

        return response()->json([
            'data' => $collections->items(),
            'total' => $collections->total(),
            'per_page' => $collections->perPage(),
            'current_page' => $collections->currentPage(),
        ]);
    }
}