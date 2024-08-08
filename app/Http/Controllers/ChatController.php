<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return Chat::all();
    }

    public function store(Request $request)
    {
        $chat = Chat::create([
            'poster' => $request->poster,
            'message' => $request->message
        ]);

        return response()->json($chat, 201);
    }

    public function show($id)
    {
        return Chat::find($id);
    }

    public function update(Request $request, $id)
    {
        $chat = Chat::findOrFail($id);
        $chat->update($request->all());

        return $chat;
    }

    public function destroy($id)
    {
        $chat = Chat::find($id);
        if ($chat) {
            $chat->delete();
            return response()->json(null, 204);
        }
        return response()->json(['error' => 'Chat not found'], 404);
    }

    public function getChatByPoster($poster)
    {
        return Chat::where('poster', $poster)->get();
    }
}