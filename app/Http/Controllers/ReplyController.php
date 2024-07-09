<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reply;
use App\Models\Topic;

class ReplyController extends Controller
{
    public function index($topicId)
    {
        $topic = Topic::findOrFail($topicId);
        return $topic->replies;
    }

    public function store(Request $request, $topicId)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $topic = Topic::findOrFail($topicId);

        $reply = new Reply($request->all());
        $reply->topic_id = $topic->id;
        $reply->save();

        return response()->json($reply, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $reply = Reply::findOrFail($id);
        $reply->content = $request->content;
        $reply->save();

        return response()->json($reply, 200);
    }

    public function destroy($id)
    {
        $reply = Reply::findOrFail($id);
        $reply->delete();

        return response()->json(null, 204);
    }
}