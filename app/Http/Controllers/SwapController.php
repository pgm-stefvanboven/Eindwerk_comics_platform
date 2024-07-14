<?php

namespace App\Http\Controllers;

use App\Models\Swap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SwapController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'comic_id' => 'required|exists:comics,id',
                'requested_comic_id' => 'required|exists:comics,id'
            ]);

            $swap = Swap::create([
                'comic_id' => $request->comic_id,
                'requested_comic_id' => $request->requested_comic_id,
                'status' => 'pending'
            ]);

            return response()->json(['message' => 'Swap request sent!', 'swap' => $swap]);
        } catch (\Exception $e) {
            Log::error('Error creating swap request: ' . $e->getMessage());
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

        return response()->json(['message' => 'Swap accepted!', 'swap' => $swap]);
    }

    public function reject(Swap $swap)
    {
        $swap->status = 'rejected';
        $swap->save();

        return response()->json(['message' => 'Swap rejected!', 'swap' => $swap]);
    }
}