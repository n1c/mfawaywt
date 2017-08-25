<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function get(Request $request)
    {
        return view('post', [
            'post' => Post::findFullByRedditIdOrFail($request->reddit_id),
        ]);
    }
}
