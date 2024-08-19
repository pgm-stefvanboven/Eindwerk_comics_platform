<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Comic;
use App\Models\ComicNote;
use Illuminate\Support\Facades\Log; // Add this line


class CollectionController extends Controller
{

    public function getComicById($id)
    {
        $comic = Comic::findOrFail($id);
        $note = ComicNote::where('comic_id', $id)->first();

        return response()->json([
            'comic' => $comic,
            'note' => $note
        ]);
    }

    // Store or update a note for a specific comic
    public function storeOrUpdateNote(Request $request, $comicId)
    {
        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        $note = ComicNote::updateOrCreate(
            [
                'comic_id' => $comicId,
            ],
            [
                'note' => $validated['note'],
            ]
        );

        return response()->json($note, 200);
    }

    // Delete a note
    public function deleteNote(Request $request, $comicId)
    {
        $noteQuery = ComicNote::where('comic_id', $comicId);

        $note = $noteQuery->first();

        if ($note) {
            $note->delete();
            return response()->json(['message' => 'Note deleted successfully'], 200);
        }

        return response()->json(['message' => 'Note not found'], 404);
    }

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