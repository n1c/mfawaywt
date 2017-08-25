<?php

namespace App\Console\Commands;

use App\Jobs\UpdatePost;
use App\Models\Post;
use Illuminate\Console\Command;

class RepollPosts extends Command
{
    protected $signature = 'app:repollposts';
    protected $description = 'Queues a job to repoll ALL posts!';

    public function handle()
    {
        $posts = Post::get();
        foreach ($posts as $p) {
            dispatch(new UpdatePost($p));
        }
    }
}
