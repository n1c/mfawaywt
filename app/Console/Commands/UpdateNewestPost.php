<?php

namespace App\Console\Commands;

use App\Jobs\UpdatePost;
use App\Models\Post;
use Illuminate\Console\Command;

class UpdateNewestPost extends Command
{
    protected $signature = 'app:updatenewestpost';
    protected $description = 'Queues an update job for the newest post';

    public function handle()
    {
        $post = Post::orderBy('created_at', 'DESC')->first();

        if (!$post) {
            return;
        }

        dispatch(new UpdatePost($post));
    }
}
