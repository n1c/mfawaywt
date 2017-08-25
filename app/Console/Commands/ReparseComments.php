<?php

namespace App\Console\Commands;

use App\Jobs\ParseComment;
use App\Models\Comment;
use Illuminate\Console\Command;

class ReparseComments extends Command
{
    protected $signature = 'app:reparsecomments {--all}';
    protected $description = 'Queues a job to reparse ALL comments!';

    public function handle()
    {
        if ($this->option('all')) {
            $comments = Comment::enabled()->get();
        } else {
            $comments = Comment::enabled()->doesntHave('images')->get();
        }

        foreach ($comments as $c) {
            dispatch(new ParseComment($c));
        }
    }
}
