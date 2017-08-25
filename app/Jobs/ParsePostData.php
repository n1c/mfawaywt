<?php

namespace App\Jobs;

use App\Jobs\UpdatePost;
use App\Libs\Reddit\API as RedditAPI;
use App\Models\Post;
use App\Models\Subreddit;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use StdClass;

class ParsePostData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(StdClass $data)
    {
        Log::debug(sprintf('Queued job %s', self::class));
        $this->data = $data;
    }

    public function handle()
    {
        if ($this->data->kind != RedditAPI::TYPE_POST) {
            Log::debug(sprintf("{%s} post isn't a post %s", self::class, $this->data->kind));
            return;
        }

        $p = $this->data->data;

        if (Post::findByRedditId($p->id)) {
            return;
        }

        $subreddit = Subreddit::findBySlugOrFail($p->subreddit);

        if (!str_contains(strtolower($p->title), strtolower($subreddit->search))) {
            Log::debug(
                sprintf("{%s} post title [%s] doesn't contain [%s]", self::class, $p->title, $subreddit->search)
            );
            return;
        }

        $post = Post::create([
            'subreddit_id' => $subreddit->id,
            'reddit_id' => $p->id,
            'title' => $p->title,
            'url' => $p->url,
            'score' => $p->score,
            'body_md' => $p->selftext,
            'created_at' => Carbon::createFromTimestampUTC($p->created_utc),
        ]);

        Log::info(sprintf("{%s} [post:%u] created", self::class, $post->id));
        dispatch(new UpdatePost($post));
    }
}
