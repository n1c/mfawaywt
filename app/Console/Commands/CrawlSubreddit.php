<?php

namespace App\Console\Commands;

use App\Libs\Reddit\API as RedditAPI;
use App\Models\Post;
use App\Models\Subreddit;
use App\Jobs\ParsePostData;
use Illuminate\Console\Command;

class CrawlSubreddit extends Command
{
    protected $signature = 'app:crawlsubreddit';
    protected $description = 'Checks a subreddit for ALL posts';

    private function getPosts(Subreddit $subreddit, string $after = null)
    {
        return RedditAPI::get('/search.json?' . http_build_query([
            'q' => $subreddit->getSearchString(),
            'sort' => 'new',
            't' => 'all',
            'after' => $after,
        ]));
    }

    public function handle()
    {
        $subChoice = $this->choice('Which Subreddit?', Subreddit::get()->pluck('slug')->toArray());
        $subreddit = Subreddit::findBySlugOrfail($subChoice);

        $hasMore = true;
        $after = null;
        do {
            $posts = self::getPosts($subreddit, $after);
            $posts = $posts->data;

            if (!isset($posts->children) || count($posts->children) == 0) {
                $hasMore = false;
                $this->danger('CrawlSubreddit response has no more posts.');
            }

            foreach ($posts->children as $p) {
                if (Post::findByRedditId($p->data->id)) {
                    $this->comment(sprintf('%s :: %s already exists.', $p->data->id, $p->data->title));
                } else {
                    $this->info(sprintf('%s :: %s new!', $p->data->id, $p->data->title));
                    dispatch(new ParsePostData($p));
                }
            }

            $after = $posts->after;
        } while ($hasMore && $after);
    }
}
