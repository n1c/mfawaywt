<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

class UpdatePost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;

    public function __construct(Post $post)
    {
        Log::debug(sprintf('{%s} queued [post:%u]', self::class, $post->id));
        $this->post = $post;
    }

    public function handle()
    {
        $post = $this->post->poll();
        if (!$post) {
            Log::info(sprintf("{%s} didn't get a good response from poll; going back on the queue!", self::class));
            $this->release(10);
        }
    }
}
