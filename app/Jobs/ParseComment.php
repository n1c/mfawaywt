<?php

namespace App\Jobs;

use App\Libs\Imgur\ImgurNotFoundException;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

class ParseComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function handle()
    {
        try {
            $this->comment->parseForImages();
        } catch (ImgurNotFoundException $e) {
            Log::error(sprintf(
                '{%s} ImgurNotFoundException in [comment:%u]',
                self::class,
                $this->comment->id
            ));

            $this->comment->disable();

            return;
        }
    }
}
