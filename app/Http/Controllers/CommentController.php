<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate(['body' => 'required']);
        Comment::create([
            'body' => $request->body,
            'user_id' => auth()->id(),
            'post_id' => $post->id,
        ]);

        return redirect()->back();
    }
}
