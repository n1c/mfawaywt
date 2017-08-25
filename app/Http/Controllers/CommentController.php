<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function get(Request $request)
    {
        return view('comment', [
            'comment' => Comment::findByRedditId($request->comment_id),
        ]);
    }
}
