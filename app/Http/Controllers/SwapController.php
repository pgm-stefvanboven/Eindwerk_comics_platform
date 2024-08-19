<?php

namespace App\Http\Controllers;

use App\Models\Swap;
use App\Models\Comic;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SwapController extends Controller
{
    public function index()
    {
        $swaps = Swap::all();
        return response()->json($swaps);
    }
    
    public function store(Request $request)
    {
        Log::info('Swap/Rental request received:', $request->all());

        try {
            $validated = $request->validate([
                'comic_id' => 'required|exists:comics,id',
                'requested_comic_id' => 'required|exists:comics,id',
                'is_rental' => 'sometimes|boolean'
            ]);

            Log::info('Validation passed:', $validated);

            // Fetch the requested comic
            $comic = Comic::findOrFail($validated['requested_comic_id']);

            // Check if there are enough comics available
            if ($comic->total > 0) {
                // Decrement the total and save
                $comic->total -= 1;
                $comic->save();

                // Create the swap or rental request
                $swap = Swap::create([
                    'comic_id' => $validated['comic_id'],
                    'requested_comic_id' => $validated['requested_comic_id'],
                    'status' => 'pending',
                    'is_rental' => $validated['is_rental'] ?? false
                ]);

                return response()->json(['message' => 'Request sent!', 'swap' => $swap]);
            } else {
                return response()->json(['error' => 'Requested comic is sold out.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error creating request: ' . $e->getMessage());
            Log::info('Comic ID: ' . $request->input('comic_id'));
            Log::info('Requested Comic ID: ' . $request->input('requested_comic_id'));
            return response()->json(['error' => 'An error occurred while creating the request.'], 500);
        }
    }

    public function accept(Swap $swap)
    {
        $swap->status = 'accepted';
        $swap->save();

        // Determine if this is a rental
        if ($swap->is_rental) {
            // Calculate the removal date (5 days from now)
            $removalDate = Carbon::now()->addDays(5);

            // Add the comic to the collection with a removal date
            Collection::create([
                'comic_id' => $swap->requested_comic_id,
                'rental_end_date' => $removalDate,
            ]);

            return response()->json([
                'message' => 'Rental accepted and comic added to collection!',
                'swap' => $swap,
                'rentedComic' => [
                    'comic_id' => $swap->requested_comic_id,
                    'rental_end_date' => $removalDate
                ]
            ]);
        } else {
            // Add the comic to the collection permanently
            Collection::create([
                'comic_id' => $swap->requested_comic_id,
            ]);

            return response()->json(['message' => 'Swap accepted and comic added to collection!', 'swap' => $swap]);
        }
    }

    public function reject(Swap $swap)
    {
        $swap->status = 'rejected';
        $swap->save();

        return response()->json(['message' => 'Request rejected!', 'swap' => $swap]);
    }
}