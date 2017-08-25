<?php

namespace App\Console\Commands;

use App\Libs\Reddit\API as RedditAPI;
use App\Models\Subreddit;
use App\Jobs\ParsePostData;
use Log;
use Illuminate\Console\Command;

class CheckPosts extends Command
{
    protected $signature = 'app:checkposts';
    protected $description = 'Checks reddit for new posts';

    public function handle()
    {
        $subreddit = Subreddit::orderBy('updated_at', 'ASC')->first();
        $subreddit->touch();

        $posts = RedditAPI::get('/search.json?' . http_build_query([
            'q' => $subreddit->getSearchString(),
            'sort' => 'new',
            't' => 'all',
        ]));

        if (!isset($posts->data->children)) {
            Log::error('CheckPosts response has no posts.');
            return;
        }

        foreach ($posts->data->children as $p) {
            dispatch(new ParsePostData($p));
        }
    }
}
