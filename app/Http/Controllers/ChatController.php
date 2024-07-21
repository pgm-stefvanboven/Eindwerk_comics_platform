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
}