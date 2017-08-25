<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Subreddit;
use App\Http\Controllers\Controller;

class WelcomeController extends Controller
{
    public function get()
    {
        $posts = Post::orderBy('created_at', 'DESC')
            ->paginate(20)
            ;

        $posts->load([
            'subreddit',
            'comments.images',
            'comments.user',
        ]);

        return view('welcome', [
            'posts' => $posts,
            'subreddits' => Subreddit::get(),
        ]);
    }
}
