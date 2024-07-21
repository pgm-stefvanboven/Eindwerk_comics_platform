<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Listing;

class ListingController extends Controller
{
    public function index()
    {
        $listings = Listing::all();
        return response()->json($listings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'poster' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'nullable|numeric',
            'type' => 'required|in:sale,rent'
        ]);

        // Verwerk de afbeelding upload
        $path = $request->file('image')->store('public/images');

        $listing = Listing::create([
            'poster' => $request->poster,
            'title' => $request->title,
            'description' => $request->description,
            'image' => Storage::url($path),
            'price' => $request->price,
            'type' => $request->type
        ]);

        return response()->json($listing, 201);
    }

    public function show($id)
    {
        $listing = Listing::findOrFail($id);
        return response()->json($listing);
    }

    public function update(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $listing->update($request->all());
        return response()->json($listing, 200);
    }

    public function destroy($id)
    {
        Listing::destroy($id);
        return response()->json(null, 204);
    }
}