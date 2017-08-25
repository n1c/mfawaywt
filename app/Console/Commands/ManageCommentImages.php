<?php

namespace App\Console\Commands;

use App\Jobs\ParseComment;
use App\Models\Comment;
use Illuminate\Console\Command;

class ManageCommentImages extends Command
{
    protected $signature = 'app:managecommentimages';
    protected $description = 'Interactive command for managing comment images';

    public function handle()
    {
        $comments = Comment::enabled()->doesntHave('images')->get();
        $this->info(sprintf('%u comments with no images.', $comments->count()));

        foreach ($comments as $c) {
            $this->info('=============================================');
            $this->info(sprintf('Comment [%u] has no images!', $c->id));
            $this->info($c->body_md);
            if ($this->confirm('Disable? [y|N]')) {
                $c->update([
                    'is_enabled' => false,
                ]);
            } else {
                // If we're not disabling; queue a reparse.
                dispatch(new ParseComment($c));
            }
        }
    }
}
