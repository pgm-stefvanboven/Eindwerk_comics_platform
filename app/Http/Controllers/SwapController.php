<?php

namespace App\Http\Controllers;

use App\Models\Swap;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SwapController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Swap request received:', $request->all());

        try {
            $validated = $request->validate([
                'comic_id' => 'required|exists:comics,id',
                'requested_comic_id' => 'required|exists:comics,id'
            ]);

            Log::info('Validation passed:', $validated);

            $swap = Swap::create([
                'comic_id' => $validated['comic_id'],
                'requested_comic_id' => $validated['requested_comic_id'],
                'status' => 'pending'
            ]);

            return response()->json(['message' => 'Swap request sent!', 'swap' => $swap]);
        } catch (\Exception $e) {
            Log::error('Error creating swap request: ' . $e->getMessage());
            Log::info('Comic ID: ' . $request->input('comic_id'));
            Log::info('Requested Comic ID: ' . $request->input('requested_comic_id'));
            return response()->json(['error' => 'An error occurred while creating the swap request.'], 500);
        }
    }

    public function index()
    {
        $swaps = Swap::all();
        return response()->json($swaps);
    }

    public function accept(Swap $swap)
    {
        $swap->status = 'accepted';
        $swap->save();

        // Add the requested comic to the collection
        Collection::create([
            'comic_id' => $swap->requested_comic_id,
        ]);

        return response()->json(['message' => 'Swap accepted and comic added to collection!', 'swap' => $swap]);
    }

    public function reject(Swap $swap)
    {
        $swap->status = 'rejected';
        $swap->save();

        return response()->json(['message' => 'Swap rejected!', 'swap' => $swap]);
    }
}