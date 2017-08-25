<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Subreddit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubRedditController extends Controller
{
    public function get(Request $request, string $subreddit)
    {
        $sub = Subreddit::findBySlugOrFail($subreddit);
        $posts = Post::orderBy('created_at', 'DESC')
            ->where('subreddit_id', $sub->id)
            ->paginate(20)
            ;

        $posts->load([ 'comments.images', 'comments.user', ]);

        return view('subreddit', [
            'posts' => $posts,
        ]);
    }
}
