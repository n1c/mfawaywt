<?php

namespace App\Console\Commands;

use App\Jobs\UpdatePost;
use App\Models\Post;
use Illuminate\Console\Command;

class UpdateLastUpdatedPost extends Command
{
    protected $signature = 'app:updatelastupdatedpost';
    protected $description = 'Queues an update job for the oldest post';

    public function handle()
    {
        $post = Post::orderBy('updated_at', 'ASC')->first();
        
        if (!$post) {
            return;
        }

        dispatch(new UpdatePost($post));
    }
}
