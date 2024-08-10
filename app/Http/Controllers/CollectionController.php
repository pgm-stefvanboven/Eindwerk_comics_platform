<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comic;
use Illuminate\Support\Facades\Log;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Comic::query();

        if ($request->has('search')) {
            $query->where('title', 'LIKE', "%{$request->search}%")
                ->orWhere('poster', 'LIKE', "%{$request->search}%")
                ->orWhere('publisher', 'LIKE', "%{$request->search}%")
                ->orWhere('description', 'LIKE', "%{$request->search}%");
        }

        $comics = $query->paginate(10);
        return response()->json($comics);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'poster' => 'required|string|max:255',
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'title' => 'required|string|max:255',
                'publisher' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'nullable|numeric',
                'type' => 'required|in:sale,rent'
            ]);

            // Handle file upload
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $path = $file->store('images', 'public'); // Store the file in the 'public/images' directory
                $validated['thumbnail'] = $path; // Save the file path in the database
            }

            $comic = Comic::create($validated);

            return response()->json($comic);
        } catch (\Exception $e) {
            Log::error('Error adding comic: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function updateRating(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $comic = Comic::findOrFail($id);
        $newRatingCount = $comic->rating_count + 1;
        $newRating = (($comic->rating * $comic->rating_count) + $validated['rating']) / $newRatingCount;

        $comic->rating = $newRating;
        $comic->rating_count = $newRatingCount;
        $comic->save();

        return response()->json($comic);
    }
}